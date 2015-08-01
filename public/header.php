<?php

#Config paramaters specific to Slim / Twig
#if needed create slimConfig.php in the same directory and
#specify the system temp (linux likely needs /tmp due to SELinux
//$ConfigTemp = $basepath.DIRECTORY_SEPARATOR."temp";

if(file_exists('slimConfig.php')){
    require_once('slimConfig.php');
}

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

$app->add(new \Slim\Middleware\SessionCookie(array('secret' => '67Hj4s3')));

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
$app->hook('slim.before', function () use ($app) {
    $posIndex = strpos( $_SERVER['PHP_SELF'], '/index.php');
    $base_url = substr( $_SERVER['PHP_SELF'], 0, $posIndex);
    $app->view()->appendData(array('baseUrl' => $base_url ));
});

$base_url = $app->router()->getCurrentRoute();

