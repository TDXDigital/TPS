<?php

session_start();

//if($_SESSION['usr']=='user')
//{
  //header('location: login.php');
//}
$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){echo "Auth Error";} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
}
else{
	echo 'ERROR!';
}
    //Get Callsign Associated with Programname
    //Retrieve from URL the showname
    $n = urldecode($_GET["n"]);
	$form = $_GET["f"];
	
	$getSQL = "select count(*) from episode where programname='" . $n ."'";
	if($form==1){
		$getSQL .= "and prerecorddate is not null";
	}
	$result = mysql_query($getSQL);
	$row = mysql_fetch_array($result);
	$count = $row['count(*)'];
	
	$hr = floor($count/60);
	$sec = $count%60;
	
	if(floor($hr/10)){
		echo $hr;
	}
	else{
		echo "0" . $hr;
	}
	echo ":";	
	if(floor($sec/10)){
		echo $sec;
	}
	else{
		echo "0" . $sec;
	}
	
	mysql_close($con);
?>