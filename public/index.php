<?php

    error_reporting(E_ALL);

    //===============================
    //   INCLUDES
    //===============================
    $path = dirname(__DIR__).DIRECTORY_SEPARATOR."vendor/autoload.php";
    if(realpath($path)){
        $path=realpath($path);
        echo $path;
    }
    else{
        echo $path;
    }
    require($path);
    print "added".$path;
    #error_log("loading: ".dirname(__FILE__)."/../vendor/autoload.php");
    //require("./vendor/autoload.php");

    $router = new \TPS\Router;

    $routes = array(
        '/' => '',
        '/test/:title' => 'Main:test@get',
        '/login'=>'Main:login@get'#,
        #'/login'=>'Main:login@post'
    );

    $router->addRoutes($routes);

    $router->set404Handler("Main:notFound");

    $router->run();
    print "done";