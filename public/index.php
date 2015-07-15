<?php
    #https://github.com/NETTUTS/Slim-MVC

    error_reporting(E_ALL);

    //===============================
    //   INCLUDES
    //===============================
    $path = dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
    //if(realpath($path)){
    //$path=realpath($path);
    //}
    require($path);

    $router = new \TPS\Router;
    $routes = array(
        '/' => '',
        '/test/:title' => 'Main:test@get',
        '/login'=>'Main:login@get'
    );
    $router->addRoutes($routes);
    $router->set404Handler("Main:notFound");
    $router->run();
