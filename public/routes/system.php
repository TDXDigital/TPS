<?php
$app->notFound(function() use ($app) {
    global $base_url, $twig;
    $params = array(
        'base_url' => $base_url,
        'title' => 'Error 404',
        'message' => "We couldn't find the page you asked for, sorry about that",
    );
    $app->render('error.html.twig',$params);
});

$app->error(function (\Exception $e) use ($app){
    try{
        $log = new \TPS\logger();
        $log->exception($e);
    } catch (Exception $ex) {
        error_log(sprintf("Unhandled Exception Occured %s,"
                . " could not log exception %s",
                $ex->getMessage(),$e->getMessage()));
    }
    $params=array(
        "statusCode" => 500,
        "title" => "Error 500",
        "message" => "Internal Server Error",
        "details" => ["&nbsp","<sub>Guru Info: </sub>",
            "<sub>".$e->getMessage()."</sub>"],
    );
    $app->render("error.html.twig",$params);
});

$dashboard = function() use ($app){
    $isXHR = $app->request->isAjax();
    $format = $app->request->get('format')?:$isXHR;
    if($isXHR || $format=="json"){
        print json_encode("Login Completed");
    }
    else{
        $app->render('dashboard.twig');
    }
};

$app->get('/', $authenticate($app), function() use ($dashboard){
    $dashboard();
});
$app->post('/', $authenticate($app), function() use ($dashboard){
    $dashboard();
});
$app->put('/', $authenticate($app), function() use ($dashboard){
    $dashboard();
});
$app->delete('/', $authenticate($app), function() use ($dashboard){
    $dashboard();
});

$app->get("/login", function () use ($app) {
   $log = new \TPS\logger();
   $flash = $app->view()->getData('flash');
   $error = '';
   if (isset($flash['error'])) {
      $error = $flash['error'];
   }
   $urlRedirect = '/';
   if ($app->request()->get('r') && $app->request()->get('r') != '/logout' && $app->request()->get('r') != '/login') {
      $_SESSION['urlRedirect'] = $app->request()->get('r');
   }
   if (isset($_SESSION['urlRedirect'])) {
      $urlRedirect = $_SESSION['urlRedirect'];
   }
   $email_value = $email_error = $srvId = $password_error = '';
   if (isset($flash['Username'])) {
      $email_value = $flash['Username'];
   }
   if (isset($flash['errors']['Username'])) {
      $email_error = $flash['errors']['Username'];
   }
   if (isset($flash['errors']['password'])) {
      $password_error = $flash['errors']['password'];
   }
   if (isset($flash['errors']['srvId'])) {
      $srvId = $flash['errors']['srvId'];
   }
   $log->debug("presented login to user via IP:",NULL,$_SERVER['REMOTE_ADDR']);
   $isXHR = $app->request->isAjax();
    if($isXHR){
        print "Login Required, please post username, password, "
        . "and serverId (srvId), See GitHub project for more information.";
    }
    else{
        $app->render('login.html.twig',
           array('error' => $error, 'Username' => $email_value,
               'Username_error' => $email_error,
               'password_error' => $password_error,
               'urlRedirect' => $urlRedirect, 'srvId'=>$srvId));
    }
});

$app->group("/system", array($authenticate($app,[2]), $requiresHttps),
        function() use ($app,$authenticate){
    $app->group("/log", $authenticate($app,[2]), function() use ($app,$authenticate){
        $app->get('/', $authenticate($app,[2]), function() use ($app){
            $log = new \TPS\logger();
            $page = $app->request->get('page')?:1;
            $limit = $app->request->get('results')?:1000;
            $severity = $app->request->get('severity');
            $start = $app->request->get('start')?:'1000-01-01 00:00:00';
            $end = $app->request->get('end')?:'9999-12-31 23:59:59';
            $user = $app->request->get('user')?:"%";
            $sort = $app->request->get('sort')?:"DESC";
            $events = $log->getLoggedMessages(
                    $page,$limit,$severity,$start,$end,$user,$sort);
            $params = array(
                "area" => "System",
                "title" => "Event Logs",
                "events" => $events,
                "page" => $page,
                "limit" => $limit,
                "severity" => $severity,
                "eventCount" => sizeof($events), #this is not correct @todo fix
            );
            $app->render("eventList.twig",$params);
        });
    });
});

$app->post("/login", function () use ($app) {
    $username = $app->request()->post('name');
    $passwordHash = $app->request()->post('p');
    $password = $app->request()->post('pass')?:$passwordHash;
    $databaseID = $app->request()->post('SRVID');
    $access = 0;
    $errors = array();

    require_once ("TPSBIN".DIRECTORY_SEPARATOR."functions.php");
    $dbxml = simplexml_load_file("TPSBIN".DIRECTORY_SEPARATOR."XML"
            .DIRECTORY_SEPARATOR."DBSETTINGS.xml");
    // check auth type
    $log = new \TPS\logger($username);
    $log->debug("Login attempt received");
    $log->startTimer();

    $stations = $log->getStations();
    $defaultStation = key($stations);
    foreach($dbxml->SERVER as $server):
        if((string)$server->ID==$databaseID):
            if((string)$server->AUTH == strtoupper("LDAP")){
                if(!extension_loaded('ldap')):
                    error_log("ldap module not installed but requested by login");
                    $e_params = array(
                        "statusCode" => 500,
                        "title" => "Internal Server Error",
                        "message" => "A login method was requested that is not supported by this server",
                    );
                    $app->render('error.html.twig',$e_params);
                endif;
                if((string)$server->ACTIVE == '0'):
                    error_log("server [".$server->ID."] was requested but is disabled in DBSETTINGS.XML");
                    $e_params = array(
                        "statusCode" => 403,
                        "title" => "Permission Denied",
                        "message" => "A login was requested that is disabled",
                    );
                    $app->render('error.html.twig',$e_params);
                endif;
                $ldap_host = (string)$server->LDP_SERVER;   // LDAP Server
                $ldap_port = (string)$server->LDP_PORT;     // LDAP Port
                $ldap_dn = (string)$server->LDP_BASE_DN;    // Active Directory Base DN
                $logo = (string)$server->LOGO_PATH;         // Logo
                $m_logo = (string)$server->MENU_LOGO_PATH;  // Menu Logo (Small)
                $ldap_usr_dom = (string)$server->LDP_DOMAIN;// Domain, for purposes of constructing $user
                $accountFilter = (string)$server->LDP_AccParam ? : 'sAMAccountName';
                $authorization = array(
                    "manager"=>["WebAdmins"],
                    "user"=>["WebUsers","Authenticated Users"]
                );

                //connect and bind
                try{
                    $ldap = ldap_connect($ldap_host,$ldap_port);
                    if($ldap_usr_dom!=''){
                        $bind = ldap_bind($ldap, $ldap_usr_dom . '\\' . $username, $password);
                    }
                    else{
                        $bind = ldap_bind($ldap, $username, $password);
                    }
                    if($bind){
                        $filter = "($accountFilter=" . $username . ")";
                        $attr = array("memberof");
                        $result = ldap_search($ldap, $ldap_dn, $filter, $attr);
                        $entries = ldap_get_entries($ldap, $result);
                        ldap_unbind($ldap);
                        $nameLDAP = substr(ldap_explode_dn($entries[0]["dn"],0)[0],3); //get username
                        foreach($entries[0]['memberof'] as $grps) {
                            foreach($authorization['manager'] as $manager_group):
                                if (strpos($grps, $manager_group)) { $access = max(array(2,$access)); break; }
                            endforeach;
                            foreach($authorization['user'] as $user_group):
                                if (strpos($grps, $user_group)) { $access = max(array(1,$access)); break;}
                            endforeach;
                        }
                        if($access>0){
                            $_SESSION['usr'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->USER);
                            $_SESSION['rpw'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD);
                            $_SESSION['access'] = $access;
                            $_SESSION['fname'] = $nameLDAP;//"LDAP Authenticated User";
                            $_SESSION['DBNAME'] = (string)$server->DATABASE;
                            if((string)$server->RESOLVE == 'URL'){
                                $_SESSION['DBHOST'] = (string)$server->URL;
                            }
                            else{
                                $_SESSION['DBHOST'] = (string)$server->IPV4;
                            }
                            $_SESSION['SRVPOST'] = (string)$server->ID;
                            $_SESSION['CALLSIGN'] = $defaultStation;
                            $_SESSION['logo']=$logo;
                            $_SESSION['m_logo']=$m_logo;
                            $_SESSION['account'] = $username;
                            $_SESSION['AutoComLimit'] = 8;
                            $_SESSION['AutoComEnable'] = TRUE;
                            $_SESSION['TimeZone']='UTC'; // this is just the default to be updated after login
                        }
                        else{
                            $app->flash('Username', $username);
                            $errors['Username'] = "Invalid username or password";
                        }
                    }
                }
                catch (Exception $ex){
                    $errors['Username'] = "Invalid login";
                }
            }
            elseif((string)$server->AUTH == strtoupper("SECL")){
                $station = substr($server->ID,0,4);
                if(!login($username, $password, NULL, $station, $server)){
                    $app->flash('Username', $username);
                    $errors['Username'] = "Invalid username or password";
                }
            }
        endif;
    endforeach;
    $duration = $log->timerDuration();
    if (count($errors) > 0) {
        $errors['srvId']=$databaseID;
        $app->flash('errors', $errors);
        $log->info("Login attempt failed (took $duration s)",
                json_encode($errors),$app->request->getIp());
        $app->redirect('/login');
    }
    if (isset($_SESSION['urlRedirect'])) {
       $tmp = $_SESSION['urlRedirect'];
       unset($_SESSION['urlRedirect']);
       $log->info("User Login (took $duration s)");
       $app->redirect($tmp);
    }
    $isXHR = $app->request->isAjax();
    if(!$isXHR){
        $app->redirect('/');
    }
    else{
        print "Login Required, please post username, password, "
        . "and serverId (srvId), See GitHub project for more information.";
    }
});

$app->get("/logout", function () use ($app) {
    $log = new \TPS\logger();
    $log->info("User Logout");
    session_unset();
    $app->view()->setData('access', null);
    session_destroy();
    $app->render('basic.twig',array('statusCode'=>'Logout','title'=>'Logout', 'message'=>'You have been logged out'));
});

$app->get("/labels/print", $authenticate($app,[2]), function() use ($app) {
    require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'legacy/opl/PrintTest.php';
});

$app->post("/webhook", function () use ($app){
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR."../lib/std/githubWebHook.php";
});

$app->get("/webhook", function () use ($app){
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR."../lib/std/githubWebHook.php";
});

$app->get("/requiressl", function () use ($app){
    $log = new \TPS\logger();
    $log->info("SSL (HTTPS) Required URL access attempted via HTTP");
    session_unset();
    $app->view()->setData('access', null);
    $app->render('basic.twig',array('statusCode'=>'No HTTPS available to complete request','title'=>'HTTP Error',
        'message'=>'No HTTPS Available but is required'));
});
