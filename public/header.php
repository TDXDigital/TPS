<?php

#Config paramaters specific to Slim / Twig
#if needed create slimConfig.php in the same directory and
#specify the system temp (linux likely needs /tmp due to SELinux
//$ConfigTemp = $basepath.DIRECTORY_SEPARATOR."temp";

if(file_exists('slimConfig.php')){
    require_once('slimConfig.php');
}

require_once 'lib' . DIRECTORY_SEPARATOR . "logger.php";

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
    header($_SERVER['SERVER_PROTOCOL'] . '500 Internal Server Error', true, 500);
    error_log("Error 500: Slim not found in TPS");
    die('500 Internal Server Error, run composer install --no-dev or composer update');
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
    'debug' => $debug,
    'view' => new \Slim\Views\Twig(),
    'log.writer' => new \TPS\logger()
));

$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires'=>$sessionExpiry,
    'name'=>$sessionName,
    'secret' => $sessionSecret,
)));

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

$base_url = $app->router()->getCurrentRoute();

$app->hook('slim.before', function () use ($app) {
    $log = new \TPS\logger(NULL,NULL,NULL,NULL,$_SERVER['REMOTE_ADDR']);
    $posIndex = strpos( $_SERVER['PHP_SELF'], '/index.php');
    $base_url = $app->request->getRootUri();
    $resuorceUri = $app->request->getResourceUri();
    $isXHR = $app->request->isAjax()?"True":"False";
    $app->view()->appendData(array('baseUrl' => $base_url ));
    $log->debug("Rendering $resuorceUri [XHR:".$isXHR."]");
});


