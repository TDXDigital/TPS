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
    error_reporting(E_ALL);

    //===============================
    //   INCLUDES
    //===============================
    require 'TPSBIN/Slim/Slim/Slim.php';
    
    \Slim\Slim::registerAutoloader();
    
    require 'TPSBIN/Slim-Views/Twig.php';
    #require 'TPSBIN/Slim-Views/Twig.php';
    // pimple required for twig
    #require 'TPSBIN/pimple/src/Pimple/ServiceProviderInterface.php';
    
    #require 'TPSBIN/slim-twig/src/Twig.php';
    
    require 'TPSBIN/twig/lib/Twig/Autoloader.php';
    Twig_Autoloader::register();

    //--------------------------
    // DB and generic functions
    //--------------------------
    require 'TPSBIN/functions.php';
    require 'TPSBIN/db_connect.php';

    //---------------------
    // API function includes
    //---------------------
    //require 'LibraryAPI.php';


    //========================
    // MAIN EXECUTION
    //========================
    $app = new \Slim\Slim(array(
        'view' => new \Slim\Views\Twig(),
        'debug' => true
    ));
    //$app->view(new Slim\Views\Twig());
    
    $view = $app->view();
    
    $view->parserOptions = array(
        'debug' => true,
        'cache' => dirname(__FILE__) . '/cache'
    );
    
    $view->parserExtensions = array(
        new \Slim\Views\Twig()
    );
    
    //----------------------------
    // Library API
    //----------------------------
    /*
    $app->get('/library/:refcode', function ($refcode) {
        print json_encode(GetLibraryRefcode($refcode));
    });
    $app->get('/library/artist/:artist', function ($artist) {
        print json_encode(GetLibraryfull($artist));
    });
    $app->get('/library/:artist/:album', function ($artist,$album) {
        print json_encode(GetLibraryfull($artist,$album));
    });
    $app->get('/library/', function () {
        print json_encode(ListLibrary());
    });*/
    $app->get('/',function() use ($app){
        $view->render('index.html.twig');
    });
    $app->run();


    /*
    require_once 'TPSBIN/twig/lib/Twig/autoloader.php';
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('TPSBIN/templates');
    $twig = new Twig_Environment($loader);

    echo $twig->render('index.html.twig', array('a_variable' => 'Fabien'));
    */
}

