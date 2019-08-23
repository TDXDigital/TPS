<?php
// user group
$app->group('/user', $authenticate, function () use ($app, $authenticate) {
    // Manage Users
    $app->get('/', $authenticate($app, [2]), function () use ($app) {
        $app->render('notSupported.twig',array('title'=>'Users'));
    });
    // Create New User
    $app->get('/new',$authenticate($app, [2]), function () use ($app) {
        $app->render('userNew.twig',array('title'=>'New User'));
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

        $password = \filter_input(INPUT_POST, 'password');
        $password = hash('sha512', $password);

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
        global $mysqli;
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
            // $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

            // // Create salted password
            // $password = hash('sha512', $password . $random_salt);
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

            // Create salted password
            $password = hash('sha512', $password . $random_salt);
            $id =-1;

            // Insert the new user into the database
            if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)"))
            {
                $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    $app->flash("error", "Registration Error: INSERT");
                }
                else
                {
                    $id = $insert_stmt->insert_id;
                    $user = new \TPS\user($_SESSION['CALLSIGN']);
                    $user->assignPermission($id, $_POST);
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
            $app->redirect("./".$id);
        }
    });
    // User page
    $app->get('/:id', function ($id) use ($app) {
        $user = new \TPS\user($_SESSION['CALLSIGN']);

        $userInfo = $user->getUserInfo($id);
        $permission = $user->getPermissions($id);

        $params = array(
            "title"=>"User Profile",
            "user"=> $userInfo,
            "permissions"=> $permission
        );
        $app->render('userNew.twig',$params);
    });
    $app->get('/:id/inbox', function ($id) use ($app) {
        $app->render('notSupported.twig', array('title'=>'User Inbox'));
    });
    $app->get('/:id/settings', function ($id) use ($app) {
        $app->render('notSupported.twig', array('title'=>'User Settings'));
    });
});
