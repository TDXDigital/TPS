<?php

    $cerl = error_reporting();
    //error_reporting(0);
    if(!isset($_SESSION)){
        session_start();
    }

    //echo "load index<br>";
    // check for installation
    if(!isset($_SESSION['DBHOST'])){
        $filename=__DIR__.DIRECTORY_SEPARATOR."TPSBIN/XML/DBSETTINGS.xml";
        if(!file_exists($filename)){
            header('location: Setup/');
        }
        else{
            require("logout.php");
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
            die("Installation has been completed or this copy of TPS may be corrupt. please check installation folder.");
        }
    }

    start:
    include_once __DIR__.DIRECTORY_SEPARATOR."TPSBIN/functions.php";
    #absolute_include("CONFIG.php", $_SERVER['PHP_SELF']);
    include_once "CONFIG.php";
    error_reporting($cerl);
    if (!isset($_SESSION)) {
        sec_session_start();
    }
    /*else{
        header('location: Security/login.html');
    }*/
    $mysqlnd = function_exists('mysqli_fetch_all');

    if ($mysqlnd||isset($_GET['strongarm'])) {
        $_SESSION['NDSupport']=TRUE;
        if(!isset($_SESSION["BASE_REF"])){
            $_SESSION['BASE_REF'] = $_SERVER['REQUEST_URI'];
        }
        if(isset($_GET['old'])){
            if($_SESSION['access']==2){
                include_once "stn/admin_old.php";
                //header("location: masterpage.php");
            }
            else{
                //include_once "djhome.php";
                include_once "stn/user_old.php";
                //header("djhome.php");
            }
        }

        else{
            if($_SESSION['access']==2){
                include_once "stn/admin.php";
                //header("location: masterpage.php");
            }
            else{
                //include_once "station/user.php";
                //include_once "djhome.php";
                include_once "stn/user_old.php";
                //header("djhome.php");
            }
        }
    }
    else{
        #echo "<span>Your server does not support mysqlnd, please enable this feature for full operations.</span>";
        $_SESSION['NDSupport']=FALSE;
        if($_SESSION['access']==2){
                include_once "stn/admin_old.php";
                //header("location: masterpage.php");
            }
            else{
                //include_once "djhome.php";
                include_once "stn/user_old.php";
                //header("djhome.php");
            }
    }
