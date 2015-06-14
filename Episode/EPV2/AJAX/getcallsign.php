<?php

session_start();

error_reporting(E_ERROR);

require '../../../TPSBIN/functions.php';
require '../../../TPSBIN/db_connect.php';


//if($_SESSION['usr']=='user')
//{
  //header('location: login.php');
//}
//$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
/*if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){echo "Auth Error";} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
}
else{
	echo 'ERROR!';
}*/
    //Get Callsign Associated with Programname
    //Retrieve from URL the showname
    $n = filter_input(INPUT_GET,'n',FILTER_SANITIZE_STRING); //urldecode($_GET["n"]);
	
	$getSQL = "select callsign from program where programname='$n' and active='1' order by callsign asc";
	$result = $mysqli->query($getSQL);
	
	if($result->num_rows===0){
		$getSQL = "select callsign,timezone from station order by callsign asc";
            $result2 = $mysqli->query($getSQL);
            $result_assoc = $result2->fetch_array(MYSQLI_ASSOC);
            date_default_timezone_set($result_assoc['timezone']);
            //$_SESSION('TimeZone');
	}
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		echo "<option value=\"".$row['callsign']."\">".$row['callsign']."</option>";
	}
	
	$mysqli->close();