<?php

#Config paramaters specific to Slim / Twig
#if needed create slimConfig.php in the same directory and
#specify the system temp (linux likely needs /tmp due to SELinux
//$ConfigTemp = $basepath.DIRECTORY_SEPARATOR."temp";

if(file_exists('slimConfig.php')){
    require_once('slimConfig.php');
}    

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
$views_path = $basepath.DIRECTORY_SEPARATOR."Views";
$temp_path = $Config_Temp?:$basepath.DIRECTORY_SEPARATOR."temp";

//load twig
if(file_exists($autoload_path)){
    require_once($autoload_path);
}    
elseif(file_exists($twig_path)&&file_exists($slim_path)){
    require_once $twig_path;
    require_once $slim_path;
    Twig_Autoloader::register();
    Slim\Slim::registerAutoloader();
}
else{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die('500 Internal Server Error');
}


// Load and set params
/*
$loader = new Twig_Loader_Filesystem($SystemViews_path);
$twig = new Twig_Environment($loader, array(
   'cache' => $temp_path,
   'debug' => $debug,
));

$escaper = new Twig_Extension_Escaper('html');
$twig->addExtension($escaper);
*/

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig(),
));
$env = $app->environment;
$app->config(array(
    'templates.path' => $views_path
));
$view = $app->view();
$view->setTemplatesDirectory($app->config('templates.path'));
$view->parserOptions = array(
    'debug' => $debug,
    'cache' => $temp_path,
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);
