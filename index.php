<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(is_null(filter_input(INPUT_GET,'twig'))){
    require_once 'legacy_controller.php';
}
else{
    require_once 'public/index.php';
    /*
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
        '/login/'=>'Main:login@post'
    );

    $router->addRoutes($routes);

    $router->set404Handler("Main:notFound");

    $router->run();*/
}
