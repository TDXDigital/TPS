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
	//if(!isset($_SESSION['LOGIN_SRC']))
	//echo "You have been logged out <br /><br /><a href=./Security/Login.html>Click Here to return to Login</a>";
    //$LOC = $_SERVER['HTTP_HOST'];
	//header("Location: ./Security/Login.html?r=0");
    header("Location: " . $SOURCE);
?>
