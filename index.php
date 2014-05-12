<?php
    include "TPSBIN/functions.php";
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
            include_once "station/user.php";
            //header("djhome.php");
        }
    }
?>
