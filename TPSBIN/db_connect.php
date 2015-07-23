<?php

date_default_timezone_set('UTC');

function findLocal($i,$path, $max,$file){
    if(file_exists($path.DIRECTORY_SEPARATOR.$file)){
        return $path;
    }
    elseif($i>$max){
        return false;
    }
    else{
        $i++;
        return findLocal($i, dirname($path), $max, "CONFIG.php");
    }
}

$TPSBIN = findLocal(0,__DIR__,10,"functions.php");

include_once $TPSBIN.DIRECTORY_SEPARATOR."functions.php";
if(session_status()===PHP_SESSION_NONE){
    session_start();
}
#error_reporting(E_ERROR);
//include_once $_SESSION['basedir']."CONFIG.php";

$timezone = $_SESSION['TimeZone'];
date_default_timezone_set($timezone);

//echo constant("HOST");
/*if(!defined("HOST") || !defined("USER") || !defined("PASSWORD") || !defined("DATABASE")){
    session_destroy();
    echo "<br>HOST:".constant("HOST");
    echo "<br>USER:".constant("USER");
    echo "<br>PASSWORD:".constant("PASSWORD");
    echo "<br>DATABASE:".constant("DATABASE");
    die("<br><br><a href=/Security/login.html?e=invalid%20params>Return to login</a>");
    //header('location: /Security/login.html?e=invalid%20params');
}*/
if(!isset($_SESSION['DBHOST'])&&$legacy){
    $_SESSION['DBHOST']=constant("HOST");
    $_SESSION['usr']=constant("USER");
    $_SESSION['rpw']=constant("PASSWORD");
    $_SESSION['DBNAME']=constant("DATABASE");
}
else{
    //assume root
    $i = 0;
    $path = findHOME($i,__DIR__,10,"CONFIG.php");
    if($path){
        require_once $path.DIRECTORY_SEPARATOR."CONFIG.php";
    }
}

include_once 'psl-config.php';   // As functions.php is not included

if(!$mysqli = new mysqli($_SESSION["DBHOST"], $_SESSION["usr"], $_SESSION["rpw"], $_SESSION["DBNAME"])){
    //header('location: /Security/login.php?e=database%20access%20denied');
    print (constant("HOST"). constant("USER"). constant("PASSWORD"). constant("DATABASE"));
    session_destroy();
    die($mysqli->connect_error." <a href='/Security/login.php?e=database%20access%20denied'>please login again</a>");
    //header('location: /Security/login.php?e=database%20access%20denied');
}
if($mysqli->connect_error)
{   
    error_log("FATAL ERROR: ".$mysqli->connect_error); // LOG PHP
    error_log("FATAL ERROR: ".$mysqli->connect_error, 4); // LOG SAPI
    die("FATAL ERROR [<span style='color:red'>".$mysqli->errno." ".$mysqli->connect_error . "</span>]</br><br/>DATABASE CONNECTION FAILED;<br><br>THIS ERROR HAS BEEN REPORTED<br><br>please <a href='logout.php'>logout</a> and try again");
}

