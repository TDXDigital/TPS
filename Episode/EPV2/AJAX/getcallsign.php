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
    $n = urldecode(filter_input(INPUT_GET,'n')?:"%"); //urldecode($_GET["n"]);
    $a = urldecode(filter_input(INPUT_GET,'a')?:1);
	echo $n ." : ". $a;
	$getSQL = $mysqli->prepare("select callsign from program where programname=? and active=? order by callsign asc");
        $getSQL->bind_param('si',$n,$a);
	if(!$getSQL->execute()){
            die($getSQL->error);
        }
        $getSQL->bind_result($callsign);
	/*
	if($result->num_rows===0){
		$getSQL = "select callsign,timezone from station order by callsign asc";
            $result2 = $mysqli->query($getSQL);
            $result_assoc = $result2->fetch_array(MYSQLI_ASSOC);
            date_default_timezone_set($result_assoc['timezone']);
            //$_SESSION('TimeZone');
	}*/
	while($getSQL->fetch()){
		echo "<option value='$callsign'>$callsign</option>";
	}
	
	$getSQL->close();