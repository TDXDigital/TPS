<?php

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
$temp_path = $basepath.DIRECTORY_SEPARATOR."temp";
 
require_once 'header.php';
require_once 'routes.php';

