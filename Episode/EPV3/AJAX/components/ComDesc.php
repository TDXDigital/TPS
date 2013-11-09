<?php
    session_start();
	
	
$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	//if(!mysqli_select_db()){/*header('Location: /login.php');*/}	
	
	$args = addslashes($_GET['desc']);
	
	//mysqli_close($con);
	$con.close();
}
?>