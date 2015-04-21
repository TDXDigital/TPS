<?php
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
error_reporting(E_ERROR);
//include_once $_SESSION['basedir']."CONFIG.php";

$timezone = $_SESSION['TimeZone'];
date_default_timezone_set($timezone);

//echo constant("HOST");
if(!defined("HOST") || !defined("USER") || !defined("PASSWORD") || !defined("DATABASE")){
    session_destroy();
    echo "<br>HOST:".constant("HOST");
    echo "<br>USER:".constant("USER");
    echo "<br>PASSWORD:".constant("PASSWORD");
    echo "<br>DATABASE:".constant("DATABASE");
    echo "<br><br><a href=/Security/login.html?e=invalid%20params>Return to login</a>";
    //header('location: /Security/login.html?e=invalid%20params');
}
elseif(!isset($_SESSION['DBHOST'])&&$legacy){
    $_SESSION['DBHOST']=constant("HOST");
    $_SESSION['usr']=constant("USER");
    $_SESSION['rpw']=constant("PASSWORD");
    $_SESSION['DBNAME']=constant("DATABASE");
}

include_once 'psl-config.php';   // As functions.php is not included

if(!$mysqli = new mysqli(constant("HOST"), constant("USER"), constant("PASSWORD"), constant("DATABASE"))){
    //header('location: /Security/login.php?e=database%20access%20denied');
    session_destroy();
    header('location: /Security/login.php?e=database%20access%20denied');
}
?>
