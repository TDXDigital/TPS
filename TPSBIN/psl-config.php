<?php

include_once 'functions.php';
if(!isset($_SESSION)){
    sec_session_start();
}

/**
 * These are the database login details
 * http://www.wikihow.com/Create-a-Secure-Login-Script-in-PHP-and-MySQL
 */  

if(defined("HOST") || isset($_SESSION['DBHOST'])){
define("HOST", $_SESSION['DBHOST']);     // The host you want to connect to.
define("USER", $_SESSION['usr']);    // The database username. 
define("PASSWORD", $_SESSION['rpw']);    // The database password. 
define("DATABASE", $_SESSION['DBNAME']);    // The database name.
}
else{
    /*$PAGE = $_SERVER['PHP_SELF'];
    $dbxml = simplexml_load_file("/TPSBIN/XML/DBSETTINGS.xml");
    $SECL_TARGET = filter_input(INPUT_POST, 'D', FILTER_SANITIZE_STRING);
    foreach( $dbxml->SERVER as $CONVAR_SECL):
        if((string)$CONVAR_SECL->ID===$SECL_TARGET){
            define("HOST", $CONVAR_SECL->HOST);     // The host you want to connect to.
            define("USER", easy_decrypt(\ENCRYPTION_KEY, $CONVAR_SECL->USER));    // The database username. 
            define("PASSWORD", easy_decrypt(\ENCRYPTION_KEY, $CONVAR_SECL->PASSWORD));    // The database password. 
            define("DATABASE", $CONVAR_SECL->DATABASE);    // The database name.
        }
    endforeach;*/
}

define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
 
define("SECURE", FALSE);    // FOR DEVELOPMENT ONLY!!!!
?>
