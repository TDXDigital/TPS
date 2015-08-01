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
#$Views_path = $basepath.DIRECTORY_SEPARATOR."Views";
/*$UserViews_path = $basepath.DIRECTORY_SEPARATOR."Views"
        .DIRECTORY_SEPARATOR."User";
$SystemViews_path = $basepath.DIRECTORY_SEPARATOR."Views"
        .DIRECTORY_SEPARATOR."System";*/
$views_path = $basepath."Views";
#$temp_path = $basepath."temp";
$temp_path = false;

require_once 'header.php';

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['access'])) {
            $_SESSION['urlRedirect'] = $app->request()->getPathInfo();
            /*$app->render('dump_session.php');
            $app->stop();*/
            $app->flash('error', 'Login required');
            $app->redirect('/login');
        }
    };
};

$app->hook('slim.before.dispatch', function() use ($app) { 
   $user = null;
   if (isset($_SESSION['usr'])) {
      $user = $_SESSION['usr'];
   }
   $app->view()->setData('usr', $user);
});
require_once 'routes.php';

