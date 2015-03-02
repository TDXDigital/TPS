<?php

    $cerl = error_reporting();
    error_reporting(0);
    include "TPSBIN/functions.php";
    sec_session_start();
    
    //echo "load index<br>";
    // check for installation
    if(!defined('HOST')||!isset($_SESSION['DBHOST'])){
        $filename="TPSBIN/XML/DBSETTINGS.xml";
        if(!file_exists($filename)){
            header('location: Setup/');
        }
        else{
            //header('location: Security/login.html?e=syserr_nchost&v='.constant('HOST').'&s='.$_SESSION['DBHOST']);
            header('location: logout.php?v='.constant('HOST').'&s='.$_SESSION['DBHOST']);
        }
    }
    else{
        // setup exists
        if(isset($_SESSION)){
            // session exists, proceed as normal
            goto start;
        }
        else{
            //unknown error.
            echo "Installation has been completed or this copy of TPS may be corrupt. please check installation folder.";
        }
    }
    
    start:
    error_reporting($cerl);
    if (!isset($_SESSION)) {
        sec_session_start();
    }
    /*else{
        header('location: Security/login.html');
    }*/
    $mysqlnd = function_exists('mysqli_fetch_all');
    
    if ($mysqlnd&&!isset($_GET['strongarm'])) {
        if(isset($_GET['old'])){
            if($_SESSION['access']==2){
                include_once "station/admin_old.php";
                //header("location: masterpage.php");
            }
            else{
                //include_once "djhome.php";
                include_once "station/user_old.php";
                //header("djhome.php");
            }
        }
    
        else{
            if($_SESSION['access']==2){
                include_once "station/admin.php";
                //header("location: masterpage.php");
            }
            else{
                //include_once "station/user.php";
                //include_once "djhome.php";
                include_once "station/user_old.php";
                //header("djhome.php");
            }
        }
    }
    else{
        echo "<span>Your server does not support mysqlnd, please enable this feature for full operations.</span>";
        if($_SESSION['access']==2){
                include_once "station/admin_old.php";
                //header("location: masterpage.php");
            }
            else{
                //include_once "djhome.php";
                include_once "station/user_old.php";
                //header("djhome.php");
            }
    }
?>
