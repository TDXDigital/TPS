<?php
	session_start();
	session_destroy();
	echo "You have been logged out <br /><br /><a href=./Security/Login.html>Click Here to return to Login</a>";
    //$LOC = $_SERVER['HTTP_HOST'];
	//header("Location: ./Security/Login.html?r=0");
    header("Location: " . $_SESSION['LOGIN_SRC']);
?>