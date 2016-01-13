<?php
//if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
// Set variables
$debug = False;
$basepath = dirname(__DIR__).DIRECTORY_SEPARATOR;
$autoload_path = $basepath."vendor".DIRECTORY_SEPARATOR."autoload.php";
$twig_path = $basepath."lib".DIRECTORY_SEPARATOR."Twig".DIRECTORY_SEPARATOR
        ."Autoloader.php";
$slim_path = $basepath."lib".DIRECTORY_SEPARATOR."Slim".DIRECTORY_SEPARATOR
        ."Slim.php";
$views_path = $basepath."Views";
$temp_path = false;
$sessionExpiry = "30minutes";
$sessionName = "TPSSlimSession";
$sessionSecret = 
        "Q7^nY{Zd'UO]Z`=L8X&`fV)Fn(LwH(vFwAm-y[z,YJD*vJj'WVYNC!+R3\cnF3I";

if(!file_exists("TPSBIN".DIRECTORY_SEPARATOR."XML".
        DIRECTORY_SEPARATOR."DBSETTINGS.xml")){
    header('Location: /Setup/');
    exit();
}

require_once 'header.php';

if($debug){
    $GLOBALS['logLevel'] = "debug";
}

/**
 * performs authentication (Not Authorization) for TPS
 * $app is the current application to be used
 * $access is a value greater than zero (public) corresponding to the security
 * clearance level required, an array can be provided if multiple levels are
 * to have access
 * $json sets the output to a JSON format instead of the standard HTML output
 */
$authenticate = function ($app,$access=0,$json=FALSE) {
    return function () use ($app,$access,$json) {
        $log = new \TPS\logger(NULL,NULL,NULL,NULL,$_SERVER['REMOTE_ADDR']);
        if (!isset($_SESSION['access'])) {
            $log->info("access to ".$app->request()->getPathInfo()
                    ." denied, Login required","redirect",
                    $_SERVER['REMOTE_ADDR']);
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/login');
        }
        elseif($access){
            if(!is_array($access)){
                $log->debug("user accessed ".$app->request()->getPathInfo(),
                        'pass',$_SERVER['REMOTE_ADDR']);
                $access = array($access);
            }
            if(!in_array($_SESSION['access'],$access)){
                $log->info("access to ".$app->request()->getPathInfo()
                    ." denied, Not Authorized",401,
                    $_SERVER['REMOTE_ADDR']);
                $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
                //$app->flash('error', 'Insufficient Permissions');
                //$app->redirect('/401');
                $app->response->setStatus(401);
                global $base_url, $twig;
                $params = array(
                    'base_url' => $app->request->getResourceUri(),
                    'title' => 'Error 401',
                    'message' => "Not Authorized",
                );
                if($json){
                    print "<h1>".$params['title']."</h1>".$params['message'];
                }
                else{
                    $app->render("error.html.twig",$params);
                }
                $app->stop(401);
            }
        }
    };
};

$requiresHttps = function () use ($app,$debug) {
    $log = new \TPS\logger(NULL,NULL,NULL,NULL,$app->request()->getIp());
    if ($app->environment['slim.url_scheme'] !== 'https' and !$debug) {
        $log->info("access to ".$app->request()->getPathInfo()
                    ." denied, HTTPS Required","redirect",
                    $_SERVER['REMOTE_ADDR']);
        $app->redirect('/requiressl');    // or render response and $app->stop();
     }
};

$app->hook('slim.before.dispatch', function() use ($app) { 
   $user = null;
   if (isset($_SESSION['fname'])) {
      $user = $_SESSION['fname'];
   }
   $uid = null;
   if (isset($_SESSION['account'])) {
      $uid = $_SESSION['account'];
   }
   $access = null;
   if (isset($_SESSION['access'])) {
      $access = $_SESSION['access'];
   }
   $app->view()->setData('userName',$user);
   $app->view()->setData('userId',$uid);
   $app->view()->setData('permissions',$access);
});
/*
$app->hook('slim.after', function() use ($app){
    $uid = null;
    if (isset($_SESSION['account'])) {
       $uid = $_SESSION['account'];
    }
    $log = new \TPS\logger($uid);
    $log->debug("Render complete ".$app->request->url);
});*/
require_once 'routes.php';

