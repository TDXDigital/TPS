<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."std".DIRECTORY_SEPARATOR."util.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."tps.php";
if(isset($_SESSION["DBHOST"])){
    require_once 'TPSBIN'.DIRECTORY_SEPARATOR.'functions.php';
    require_once 'TPSBIN'.DIRECTORY_SEPARATOR.'db_connect.php';
    require_once 'lib_api'.DIRECTORY_SEPARATOR.'LibraryAPI.php';
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."notifications.php";
    $tps = new \TPS\TPS();
    $allStations = $tps->getStations();
    $app->hook('slim.before.dispatch', function() use ($app, $allStations) {
        try {
            $notifications = new \TPS\notification(\TPS\util::get($_SESSION, 'CALLSIGN'));
            $broadcasts = $notifications->listUserNotifications();
            $messages = \TPS\notification::convertToMessageFormat($broadcasts);
            $app->view()->setData('messages', $messages);
        } catch (Exception $e) {
            $route = $app->router()->getCurrentRoute()->getPattern();
            if($route!="/updates") {
                $app->flash("error", "Critical Exception Occured: ".$e->getMessage()."<br>Updates Likely Required");
                $app->redirect('/updates');
            }
            $app->view()->setData('messages', \TPS\notification::convertToMessageFormat([
            array(
                "message"=>"Critical: ".$e->getMessage()
            )
            ]));
        }
        try{
            $app->view()->setData('environmentCurrentStation', $_SESSION['CALLSIGN']);
            $app->view()->setData('environmentAllStations', $allStations);
        } catch (\Exception $e){

        }
    });
}
require_once 'routes'.DIRECTORY_SEPARATOR.'system.php';

/*$app->get('/', $authenticate($app), function() use ($app){
    $params = array();
    $app->render('dashboard.twig',$params);
});
$app->post('/', $authenticate($app), function() use ($app){
    $app->render('dashboard.twig');
});*/

$app->get('/updates', $authenticate, function () use ($app) {
    $updates = scandir("./Update/proc/");
    $updateList=array();
    $update_JSON = array();
    foreach ($updates as $update){
        if(strtolower(substr($update,-5))==='.json'){
            $update_JSON[$update]=json_decode(file_get_contents('./Update/proc/'.$update),true);
            $updateList[$update]=$update_JSON[$update]['TPS_Errno'];
        }
    }
    $params = array(
        'updateList'=>json_encode($updateList),
        'updates'=>$update_JSON,
        'title'=>'System Updates',

        );
    $app->render('update.twig',$params);
});
// user group
$app->group('/user', $authenticate, function () use ($app, $authenticate) {
    // Manage Users
    $app->get('/', $authenticate($app, [2]), function () use ($app) {
        $app->render('notSupported.twig',array('title'=>'Users'));
    });
    // Create New User
    $app->get('/new',$authenticate($app, [2]), function () use ($app) {
        $app->render('notSupported.twig',array('title'=>'New User'));
    });
    $app->post('/new',$authenticate($app, [2]), function () use ($app) {
        $error_msg = "";
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Not a valid email
            $error_msg .= '<p class="error">The email address you entered is not valid</p>';
        }

        $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
        if (strlen($password) != 128) {
            // The hashed pwd should be 128 characters long.
            // If it's not, something really odd has happened
            $error_msg .= '<p class="error">Invalid password configuration.</p>';
        }
        if(empty($email) || empty($username) || empty($password)){
            if(!empty($error_msg)){
                $app->halt(400, $error_msg);
            }
            else {
                $app->halt(400, "invalid values provided in post");
            }
        }

        // Username validity and password validity have been checked client side.
        // This should should be adequate as nobody gains any advantage from
        // breaking these rules.
        //

        $prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
        $stmt = $mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // A user with this email address already exists
                $error_msg .= '<p class="error">A user with this email address already exists.</p>';
            }
        } else {
            $error_msg .= '<p class="error">Database error</p>';
            $error_msg .= $mysqli->error;
        }

        // TODO:
        // We'll also have to account for the situation where the user doesn't have
        // rights to do registration, by checking what type of user is attempting to
        // perform the operation.

        if (empty($error_msg)) {
            // Create a random salt
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

            // Create salted password
            $password = hash('sha512', $password . $random_salt);

            // Insert the new user into the database
            if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
                $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    $app->flash("error", "Registration Error: INSERT");
                }
            }
        }
        else{
            $app->flash("error", $error_msg);
        }
        $isXHR = $app->request->isAjax();
        if($isXHR){
            standardResult::created($app, "created", null);
        }
        else{
            $app->redirect("./".$username);
        }
    });
    // User page
    $app->get('/:id', function ($id) use ($app) {
        $app->render('notSupported.twig',array('title'=>'User Profile'));
    });
    $app->get('/:id/inbox', function ($id) use ($app) {
        $app->render('notSupported.twig', array('title'=>'User Inbox'));
    });
    $app->get('/:id/settings', function ($id) use ($app) {
        $app->render('notSupported.twig', array('title'=>'User Settings'));
    });
});

require_once 'lib'.DIRECTORY_SEPARATOR.'libs.php';
require_once 'routes'.DIRECTORY_SEPARATOR.'routes.php';

$app->run();
