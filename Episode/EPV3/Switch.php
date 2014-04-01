<?php
	session_start();
	$ROOT = addslashes($_GET['q']);
    $BASE = ".";
    if($ROOT=='V2'){
        $BASE="./EPV3";
    }
	$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
	
	if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
	else if($con){
		if(!mysql_select_db($_SESSION['DBNAME'])){die("Error connecting to switch reporting database");}
	}
	else{
		echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
	}
	$sql = "select * from switchstatus ORDER BY ID DESC limit 1 ";
	/*echo "<span style=\"font-size:9px;\">ACS 8.2 Plus Switch Status</span><table>
	<tr>";
	for($i = 1; $i < 9; $i++){
		echo "<th>" . $i . "</th>";
	}*/
	//echo "<th>S</th></tr>";//<tr>";
    echo "<span style=\"font-size:9px;\">ACS 8.2 Plus Switch Status</span><br><span>";
	$result = mysql_query($sql);
	$srr = mysql_fetch_array($result);
	$track = 0;
    $title = 1;
	for($i = 16; $i > 0; $i=$i-2){
		$dl = substr($srr['Bank1'],($i*(-1)),1); 	
		if($dl=='0'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/GreenOff.png\" title=\"Switch &#35;1 - $title\" alt=\"0\"/></td>";
		}	
		else if($dl=='1'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/GreenOn.png\" title=\"Switch &#35;1 - $title\" alt=\"1\"/></td>";
		}
		else{
			echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"Switch &#35;1 - $title\" alt=\"2\"/></td>";
		}
		$title++;
	}
    $title = "Broadcast Silence Sensor";
	$silence = $srr['SS'];
	$SS1 = substr($silence,-1);
	if($SS1 == "0"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$title\" alt=\"0\"/></td>";
	}
	else if($SS1 == "1"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOn.png\" title=\"$title\" alt=\"1\"/></td>";
	}
	else{
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$SS1\" alt=\"2\"/></td>";
	}
	//echo "</tr><tr>";
    echo "</span><br><span>";
    $title = 1;
	for($i = 16; $i > 0; $i=$i-2){
		$dl = substr($srr['Bank2'],($i*(-1)),1); 	
		if($dl=='0'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/RedOff.png\" title=\"Switch &#35;2 - $title\" alt=\"0\"/></td>";
		}	
		else if($dl=='1'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/RedOn.png\" title=\"Switch &#35;2 - $title\" alt=\"1\"/></td>";
		}
		else{
			echo "<td><img src=\"$BASE/Images/LIGHTS/RedOff.png\" title=\"Switch &#35;2 - $title\"alt=\"2\"/></td>";
		}
		$title++;
	}
	$title = "Record Silence Sensor";
	$SS2 = substr($silence,-2,-1);
	if($SS2 == "0"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$title\" alt=\"0\"/></td>";
	}
	else if($SS2 == "1"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOn.png\" title=\"$title\" alt=\"1\"/></td>";
	}
	else{
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$SS2\"/></td>";
	}
    echo "</span>";
	/*echo "</tr>";
	echo "</table>";*/
	//echo "<span>Timespamp: ".$srr['timestamp']."</span>";
?>