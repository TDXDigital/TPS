<?php
    $path = dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
    //if(realpath($path)){
    //$path=realpath($path);
    //}
    require_once($path);
    
    /*$smarty = new Smarty();
    $smarty->template_dir = dirname(__DIR__).DIRECTORY_SEPARATOR."Views";
    $smarty->compile_dir = dirname(__DIR__).DIRECTORY_SEPARATOR."temp";*/
    
    //based on input (SLIM?) call different functions, these functions should
    // return a SMARTY template that can be used in $engine->display();
    /*  EG http://www.smarty.net/v3_overview
     * $tpl = $smarty->createTemplate('my.tpl');
     * $tpl->assign('foo','bar');
     * $smarty->display($tpl); // or $tpl->display();
     */
    
    /*$tpl = $smarty->createTemplate('index.html.twig');
    $tpl->display();*/
    #https://github.com/NETTUTS/Slim-MVC

    /*error_reporting(E_ALL);

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
     * 
     */
