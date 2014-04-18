<?php
    include "TPSBIN/functions.php";
    sec_session_start();

    if($_SESSION['access']==2){
        include_once "masterpage.php";
    }
    else{
        include_once "djhome.php";
    }
?>