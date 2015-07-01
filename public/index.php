<?php

    error_reporting(E_ALL);

    //===============================
    //   INCLUDES
    //===============================
    require("vendor/autoload.php");

    $router = new \TPS\Router;

    $routes = array(
        '/' => '',
        '/test/:title' => 'Main:test@get',
        '/login'=>'Main:login@get',
        '/login'=>'Main:login@post'
    );

    $router->addRoutes($routes);

    $router->set404Handler("Main:notFound");

    $router->run();