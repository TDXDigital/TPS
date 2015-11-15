<?php

/**
 *  Place a copy of this file in the root directory of your webserver
 * public http docs, this will allow overriding of the default slim paramaters
 * Unless you are doing development or troubleshooting, it is recommended to
 * leave $debug set to False
 */


// Set variables
$debug = False;
$basepath = '';
$autoload_path = $basepath."vendor".DIRECTORY_SEPARATOR."autoload.php";
$twig_path = $basepath."lib".DIRECTORY_SEPARATOR."Twig".DIRECTORY_SEPARATOR
        ."Autoloader.php";
$slim_path = $basepath."lib".DIRECTORY_SEPARATOR."Slim".DIRECTORY_SEPARATOR
        ."Slim.php";
$views_path = $basepath."Views";
$temp_path = false;
$sessionExpiry = "30minutes";
$sessionName = "TPSSlimSession";
$sessionSecret = 
        "Q7^nY{Zd'UO]Z`=L8X&`fV)Fn(LwH(v4dfS2;'{*vJj'WVYNC!+R3\cnF3I";