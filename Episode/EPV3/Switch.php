<meta http-equiv="refresh" content="30">
<?php
	session_start();
	
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
	echo "<span style=\"font-size:9px;\">ACS 8.2 Plus Switch Status</span><table>
	<tr>";
	for($i = 1; $i < 9; $i++){
		echo "<th>" . $i . "</th>";
	}
	echo "<th>S</th></tr><tr>";
	$result = mysql_query($sql);
	$srr = mysql_fetch_array($result);
	$track = 0;
	for($i = 16; $i > 0; $i=$i-2){
		$dl = substr($srr['Bank1'],($i*(-1)),1); 	
		if($dl=='0'){
			echo "<td><img src=\"Images/LIGHTS/GreenOff.png\" alt=\"0\"/></td>";
		}	
		else if($dl=='1'){
			echo "<td><img src=\"Images/LIGHTS/GreenOn.png\" alt=\"1\"/></td>";
		}
		else{
			echo "<td><img src=\"Images/LIGHTS/GreenOff.png\" alt=\"2\"/></td>";
		}
		
	}
	$Silence = $srr['SS'];
	$SS1 = substr($silence,-3,1);
	if($SS1 == "0"){
		echo "<td><img src=\"Images/LIGHTS/RedOff.png\" alt=\"0\"/></td>";
	}
	else if($SS1 == "1"){
		echo "<td><img src=\"Images/LIGHTS/RedOn.png\" alt=\"1\"/></td>";
	}
	else{
		echo "<td><img src=\"Images/LIGHTS/RedOff.png\" alt=\"2\"/></td>";
	}
	echo "</tr><tr>";
	for($i = 16; $i > 0; $i=$i-2){
		$dl = substr($srr['Bank2'],($i*(-1)),1); 	
		if($dl=='0'){
			echo "<td><img src=\"Images/LIGHTS/RedOff.png\" alt=\"0\"/></td>";
		}	
		else if($dl=='1'){
			echo "<td><img src=\"Images/LIGHTS/RedOn.png\" alt=\"1\"/></td>";
		}
		else{
			echo "<td><img src=\"Images/LIGHTS/RedOff.png\" alt=\"2\"/></td>";
		}
		
	}
	
	$SS2 = substr($silence,-1,1);
	if($SS2 == "0"){
		echo "<td><img src=\"Images/LIGHTS/RedOff.png\" alt=\"0\"/></td>";
	}
	else if($SS2 == "1"){
		echo "<td><img src=\"Images/LIGHTS/RedOn.png\" alt=\"1\"/></td>";
	}
	else{
		echo "<td><img src=\"Images/LIGHTS/RedOff.png\" alt=\"" . $SS2 . "\"/></td>";
	}
	echo "</tr>";
	echo "</table>";
	//echo "<span>Timespamp: ".$srr['timestamp']."</span>";
?>