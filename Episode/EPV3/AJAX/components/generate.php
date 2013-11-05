<?php

function to12hour($hour1){ 
	// 24-hour time to 12-hour time 
	return DATE("g:i a", STRTOTIME($hour1));
}
function to24hour($hour2){
	// 12-hour time to 24-hour time 
	return DATE("H:i", STRTOTIME($hour2));
}
 

    session_start();
	unset($_SESSION['program']);
	unset($_SESSION['time']);
	unset($_SESSION['date']);
	$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
	$friends = array();
	if (!$con){
		die("<h2>Error " . mysql_errno() . "</h2><p>Could not establish connection to database. Authentication failed</p>");
	}
	else{
		if(!mysql_select_db("CKXU")){
			die("<h2>Error " . mysql_errno() . "</h2><p>User Access Error. Database refused connection</p>");
		}
		
		$program=addslashes($_POST['p']);
		$timeBuff=addslashes($_POST['t']);
		$time=to24hour($timeBuff);
		$date=addslashes($_POST['d']);
		$call=addslashes($_POST['callsign']);
		$Type=addslashes($_POST['brType']);
		$prdate=addslashes($_POST['prdate']);
		$desc=addslashes($_POST['Description']);
		if($Type!="0"){
			$inssql = "INSERT INTO episode (callsign,programname,date,starttime,prerecorddate,Type,Description) VALUES ('".$call."','".$program."','".$date."','".$time."','".$prdate."','".$Type."','".$desc."')";
		}
		else{
			$inssql = "INSERT INTO episode (callsign,programname,date,starttime,Type,Description) VALUES ('".$call."','".$program."','".$date."','".$time."','".$Type."','".$desc."')";
		}
		if(!$result = mysql_query($inssql)){
			if(mysql_errno()==1062){
				header("location: ../../logs.php?c=".$call."&d=".$date."&p=".$program."&t=".$time."&msg=Loaded+Log+from+previous+information.+Could+not+create+new+log");
			}
			die("<h2>Error " . mysql_errno() . "</h2><p>Send Error, Could not create log<br/<i>".mysql_error()."</p>");
		}
		else{
			$_SESSION['program'] = $program;
			$_SESSION['time'] = $time;
			$_SESSION['date'] = $date;
			$_SESSION['callsign'] = $call;
			header("location: ../../logs.php");
		}		
	}
?>