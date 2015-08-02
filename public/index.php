<?php
//if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

// Set variables
$debug = TRUE;
$basepath = dirname(__DIR__).DIRECTORY_SEPARATOR;
$autoload_path = $basepath."vendor".DIRECTORY_SEPARATOR."autoload.php";
$twig_path = $basepath."lib".DIRECTORY_SEPARATOR."Twig".DIRECTORY_SEPARATOR
        ."Autoloader.php";
$slim_path = $basepath."lib".DIRECTORY_SEPARATOR."Slim".DIRECTORY_SEPARATOR
        ."Slim.php";
$views_path = $basepath."Views";
$temp_path = false;

require_once 'header.php';

$authenticate = function ($app,$access=0) {
    return function () use ($app,$access) {
        if (!isset($_SESSION['access'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login required');
            $app->redirect('/login');
        }
        elseif($access){
            if(!is_array($access)){
                $array = array($access);
            }
            if(!in_array($_SESSION['access'],$access)){
                $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
                $app->flash('error', 'Insufficient Permissions');
                $app->redirect('/login');
            }
        }
    };
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
require_once 'routes.php';

