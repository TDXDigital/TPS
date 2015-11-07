<?php
	session_start();
	unset($_SESSION["program"]);
	unset($_SESSION["time"]);
	unset($_SESSION["date"]);
	unset($_SESSION["callsign"]);
	$dest=$_GET['dest'];
	//session_destroy();
	header("location: $dest?c=dest");
?>