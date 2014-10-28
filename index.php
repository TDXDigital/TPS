<?php
    include "TPSBIN/functions.php";
    
    // check for installation
    if(!defined('DBHOST')){
        $filename="TPSBIN/XML/DBSETTINGS.xml";
        if(!file_exists($filename)){
            header('location: Setup/');
        }
        else{
            goto start;
        }
    }
    else{
        //go to install script if exists, otherise display error
        if(false){
            
        }
        else{
            echo "Installation has been completed or this copy of TPS may be corrupt. please check installation folder.";
        }
    }
    
    start:
    if (!isset($_SESSION)) {
        sec_session_start();
    }
    /*else{
        header('location: Security/login.html');
    }*/
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
?>
