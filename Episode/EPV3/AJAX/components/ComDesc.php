<?php
    session_start();
	
	
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){/*header('Location: /login.php');*/}	
	
	$args = addslashes($_GET['desc']);
	
	mysql_close($con);
	//$con.close();
}
?>