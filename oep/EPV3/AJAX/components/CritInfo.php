<?php
    session_start();
	
	function to12hour($hour1){ 
		// 24-hour time to 12-hour time 
		return DATE("g:i a", STRTOTIME($hour1));
	}
	function to24hour($hour2){
		// 12-hour time to 24-hour time 
		return DATE("H:i", STRTOTIME($hour2));
	}
	
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){/*header('Location: /login.php');*/}	

	// GLOBAL SETTINGS
	$SETW = "1350px";
	
	// FETCH UNIVERSAL POST VALUES
	if(isset($_SESSION['program'])){
		$SHOW = $_SESSION['program'];
	}
	else{
		$SHOW = "NULL";
	}
	
	if(isset($_SESSION['time'])){
		$START = $_SESSION['time'];
	}
	else{
		$START = "00:00:00";
	}
	
	if(isset($_SESSION['date'])){
		$DATE = $_SESSION['date'];
	}
	else{
		$DATE = date("Y-m-d");
	}
	
	if(isset($_SESSION['callsign'])){
		$CALL = addslashes($_SESSION['callsign']);
	}
	else{
		$CALL = "NULL";
	}
	//CODE
		//##########################//
		// Check Switch Status      //
		//##########################//
		$switchqu = "select * from switchstatus ORDER BY ID DESC limit 1 ";
		$switchre = mysql_query($switchqu);
		$switchArray = mysql_fetch_array($switchre);
		$broadcastcheck = $switchArray['Bank1'];
		$RadioDJ = substr($broadcastcheck, -16 , 1 );
		$booth1 = substr($broadcastcheck, -14 , 1 );
		$booth2 = substr($broadcastcheck, -12, 1 );
		
		// END Switch Check
		
		$sql_select = "SELECT * FROM socan WHERE Enabled = '1' AND '".$DATE."' BETWEEN socan.start and socan.end;";
		$result = mysql_query($sql_select);
		
		$count = 0;
		if(mysql_num_rows($result)>0|| $RadioDJ=="1" ){
			echo "<script>
	function CritHide(id){
		$('#'+id).slideUp();
		$('#exp').slideDown();
	}
	function CritShow(){
		$('[id^=\"warning_\"]').slideDown();
		$('#exp').slideUp();
	}
</script>
<h3 id=\"exp\" style=\"display: none;\">&nbsp;
<span style=\"float: right;\" class=\"ui-icon ui-icon-refresh\" title=\"Refresh\" onclick=\"CritUpdate()\"></span>
<span style=\"float: right;\" class=\"ui-icon ui-icon-plus\" title=\"Show Warnings\" onclick=\"CritShow()\"></span></h3>";
		}
		if($RadioDJ == "1"){
			//array_push($warning,"<strong><br/>Warning: At " . substr($switchArray['timestamp'],-8,5) . " the 24 Hour system was live to air<br/><br/></strong>");
			echo "<h3 id=\"warning_24hr\" style='text-align: center;'><img src='Images/Audit.png' alt='24 Hour' />&nbsp;Warning: At " . substr($switchArray['timestamp'],-8,5) . " the 24 Hour system was live to air";
			echo "<span style=\"float: right;\" class=\"ui-icon ui-icon-refresh\" title='Refresh' onclick=\"CritUpdate()\"></span>
			<span style='float: right' class='ui-icon ui-icon-minus' title='Hide Warning' onclick=\"return CritHide('warning_24hr')\"></span></h3>";
		}
		while($row = mysql_fetch_array($result)){
			echo "<h3 id='warning_".$count."' style='text-align: center;'><img src='Images/Audit.png' alt='Audit Cone - ' />&nbsp;AUDIT WARNING : This station is currently under a federal audit, you <strong>
			<i><u>MUST</u></i></strong> provide the composer(s) in addition to standard information
			<span style=\"float: right;\" class=\"ui-icon ui-icon-refresh\" title='Refresh' onclick=\"CritUpdate()\"></span>
			<span style='float: right' class='ui-icon ui-icon-minus' title='Hide Warning' onclick=\"return CritHide('warning_".$count."')\"></span></h3>";
			$count++;
		}		
	//END CODE
}
else{
	echo 'ERROR!';
}
mysql_close($con);
?>
