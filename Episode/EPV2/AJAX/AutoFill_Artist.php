<?php
    session_start();
	
	$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
	
	if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
	else if($con){
		if(!mysql_select_db("ckxu")){die("Error connecting to switch reporting database");}
	}
	else{
		echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
	}
?>