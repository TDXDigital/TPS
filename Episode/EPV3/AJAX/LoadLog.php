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
$from = $_SERVER['HTTP_REFERER'];
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	session_destroy();
	header('Location: ../logs.php?msg=Authentication Error - Database Access Denied');
}
else if($con){
	if(!mysql_select_db("CKXU")){
		session_destroy();
		header("Location: $from?msg=Authentication Error - Database denied access to selected station\'s records ");
	}
	else{
		$BARCODE = addslashes($_POST['barcode']);
		//////////////////////////////////////////////
		//											//
		//	Display Choices if more than one		//
		//	Otherwise set session for one result	//
		//											//
		//////////////////////////////////////////////
		
		$query = "SELECT * FROM episode where EpNum='".$BARCODE."'";
		$shows = mysql_query($query);
		if(mysql_num_rows($shows)==1){
			$showresult = mysql_fetch_array($shows);
			$_SESSION['program'] = $showresult['programname'];
			$_SESSION['date'] = $showresult['date'];
			$_SESSION['time'] = $showresult['starttime'];
			$_SESSION['callsign'] = $showresult['callsign'];
		}
		header("location: $from?disable=true&load=true&argm=No Record Found");
		
	}
}	
?>