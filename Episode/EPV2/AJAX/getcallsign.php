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
	
	$getSQL = "select callsign from program where programname='" . $n ."' and active='1' order by callsign asc";
	$result = mysql_query($getSQL);
	
	if(mysql_num_rows($result)==0){
		$getSQL = "select callsign from station order by callsign asc";
	$result = mysql_query($getSQL);
	}
	while($row = mysql_fetch_array($result)){
		echo "<option value=\"".$row['callsign']."\">".$row['callsign']."</option>";
	}
	
	mysql_close($con);
?>