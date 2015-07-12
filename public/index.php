<?php

    error_reporting(E_ALL);

    //===============================
    //   INCLUDES
    //===============================
    $path = dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
    if(realpath($path)){
        $path=realpath($path);
    }
    require($path);

    $router = new \TPS\Router;
    print "<br>"."Loaded Router";
    $routes = array(
        '/' => '',
        '/test/:title' => 'Main:test@get',
        '/login'=>'Main:login@get'
    );
    print "<br>"."Loaded Routes Array";
    $router->addRoutes($routes);
    print "<br>"."Set Routes";
    $router->set404Handler("Main:notFound");
    print "<br>"."Added 404 Handeler";
    $router->run();
    print "<br>"."Ran Run";