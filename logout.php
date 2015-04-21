<?php
	session_start();
	if(!isset($_SESSION['LOGIN_SRC'])){
		$SOURCE = "Security/Login.html?r=0";
	}
	else{
		$SOURCE = $_SESSION['LOGIN_SRC'];
	}
	session_unset();
	session_destroy();
    //$LOC = $_SERVER['HTTP_HOST'];
	//header("Location: ./Security/Login.html?r=0");
    error_reporting(0);
    /*if($_SESSION['LOGIN_SRC']!=""){
        header("Location: " . $_SESSION['LOGIN_SRC']);
       //echo "LOGIN SEC KNOWN";
    }
    else{*/
        $current = strlen($_SERVER['PHP_SELF']);
        $current -= strlen(basename($_SERVER['PHP_SELF']));
        $path = substr($_SERVER['PHP_SELF'],0,$current);
        
        header("Location: ".$path."Security/Login.html");
        
    //}
    
    //echo "You have been logged out <br /><br /><a href=./Security/Login.html>Click Here to return to Login</a>";
?>
