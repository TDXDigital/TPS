<?php
session_start();
if(isset($_SESSION['slim.flash'])){
    header('Location: ./logout');
}
else{
    var_dump($_SESSION);
    if(!isset($_SESSION['LOGIN_SRC'])){
            $SOURCE = "Security/Login.html?r=0";
    }
    else{
            $SOURCE = $_SESSION['LOGIN_SRC'];
    }
    session_unset();
    session_destroy();
    session_commit();

    $current = strlen($_SERVER['PHP_SELF']);
    $current -= strlen(basename($_SERVER['PHP_SELF']));
    $path = substr($_SERVER['PHP_SELF'],0,$current-1);

    header("Location: ".$path."");
}
