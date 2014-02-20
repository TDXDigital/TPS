<?php
    include_once "../../../../TPSBIN/functions.php";
    sec_session_start();
	
	function to12hour($hour1){ 
		// 24-hour time to 12-hour time 
		return DATE("g:i a", STRTOTIME($hour1));
	}
	function to24hour($hour2){
		// 12-hour time to 24-hour time 
		return DATE("H:i", STRTOTIME($hour2));
	}
	
$con = mysql_connect($_SESSION['HOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){/*header('Location: /login.php');*/}	

	// GLOBAL SETTINGS
	//$SETW = "1350px";
	
	// FETCH UNIVERSAL POST VALUES
	if(isset($_SESSION['program'])){
		$SHOW = addslashes($_SESSION['program']);
	}
	else{
		$SHOW = "NULL";
	}
	
	if(isset($_SESSION['time'])){
		$START = addslashes(to24Hour($_SESSION['time']));
	}
	else{
		$START = "00:00:00";
	}
	
	if(isset($_SESSION['date'])){
		$DATE = addslashes($_SESSION['date']);
	}
	else{
		$DATE = date("Y-m-d");
	}
	
	if(isset($_SESSION['callsign'])){
		$CALL = addslashes($_SESSION['callsign']);
	}
	else{
		$CALL = "NULL";
	}/*
	$ccfill = 0;
	$ccf = "SELECT count(songid) from song where callsign='".$CALL."' and starttime='".$START."' and programname='".$SHOW."' and date='".$DATE."' and cancon='1'";
	$resultarrcf = mysql_query($ccf);
	$ccfarr = mysql_fetch_array($resultarrcf);
	echo "<div id='ccfill'>".$ccfarr['count(songid)']."</div>";
	$ccreq = 0;
	$ccr = "SELECT ";
	$resultarrcr = mysql_query($ccf);
	$ccfarr = mysql_fetch_array($resultarrcr);
	echo "<div id='ccfill'>".$ccfarr['count(songid)']."</div>";*/
	
	//////////////////////////////////////////
	//										//
	//		 SECTION SHOULD BE UPDATED		//
	//										//
	////////////////////////////////////////// 
	
		$SQLProg = "SELECT Genre.*, Program.length from Genre, Program where Program.programname=\"" . $SHOW . "\" and program.callsign=\"" . $CALL . "\" and Program.genre=Genre.genreid";
		if(!($result = mysql_query($SQLProg))){
			echo "Program Error 001 " . mysql_error();
		}
		if(!($Requirements = mysql_fetch_array($result))){
			echo "Program Error 002 " . mysql_error();
		}
		$SQL2PR = "SELECT * from Program where programname=\"" . $SHOW . "\" and callsign=\"" . $CALL . "\" ";
		if(!($result2 = mysql_query($SQL2PR))){
			echo "Program Error 003 " . mysql_error();
		}
		if(!($Req2 = mysql_fetch_array($result2))){
			echo "Program Error 004 " . mysql_error();
		}
		
		if($Req2['CCX']!='-1'){
			$CC = ceil($Req2['CCX'] * $Requirements['length'] / 60);
		}
		else{
			$CC = ceil($Requirements['cancon'] * $Requirements['length'] / 60);
		}
		if($Req2['PLX']!='-1'){
			$PL = ceil($Req2['PLX'] * $Requirements['length'] / 60);
		}
		else{
			$PL = ceil($Requirements['playlist'] * $Requirements['length'] / 60);
		}
		
		//$PL = ceil($Requirements['playlist'] * $Requirements['length'] / 60);
		$CLA = $Requirements['genreid'];
		if(!isset($CLA)){
			$CC = "0";
			$PL = "0";
			$CLA = "Not Set";
		}
		
		// COUNT CANCON
		$SQLCOUNTCC = "Select songid from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and cancon='1' ";
		$resultCC = mysql_query($SQLCOUNTCC);
		$RECCC = mysql_num_rows($resultCC);
		
		// COUNT HITS
		$SQLCOUNTHI = "Select songid from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and hit='1' ";
		$resultHI = mysql_query($SQLCOUNTHI);
		$RECHI = mysql_num_rows($resultHI);
		
		// COUNT PLAYLIST
		$SQLCOUNTPL = "Select songid from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and playlistnumber IS NOT NULL ";
		if($SETTINGS['ST_PLLG']=='1'){
			$SQLCOUNTPL .="group by playlistnumber";	
		}
		$resultPL = mysql_query($SQLCOUNTPL);
		$RECPL = mysql_num_rows($resultPL);
		
		//COUNT ADS
		$SQLCOUNT51 = "Select songid from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category='51' and AdViolationFlag is null";
		$result51 = mysql_query($SQLCOUNT51);
		$REC51 = mysql_num_rows($result51);
		
		//COUNT PSA
		$SQLCOUNTPROMO = "Select songid from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category='45'";
		$SQLCOUNTPSA = "Select songid from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category like '1%' and title like '%PSA%' ";
		//$SQLCOUNTPSA = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and category like '1%' and title like '%Promo%' ";
		
		$resultPSA = mysql_query($SQLCOUNTPSA);
		$resultPROMO = mysql_query($SQLCOUNTPROMO);
		$RECPSA = mysql_num_rows($resultPROMO);
		$RECPSA += mysql_num_rows($resultPSA);
		
		$ADREQ = ceil(1 * $Requirements['length'] / 60);
		$PSAREQ = ceil(2 * $Requirements['length'] / 60);
		$HITLIM = $Req2['HitLimit'];
		
		// NEW STYLE CODE
		
		$ccfill = 0;
		/*$ccf = "SELECT count(songid) from song where callsign='".$CALL."' and starttime='".$START."' and programname='".$SHOW."' and date='".$DATE."' and cancon='1'";
		$resultarrcf = mysql_query($ccf);
		$ccfarr = mysql_fetch_array($resultarrcf);*/
		echo "<div id='ccfill'>".$RECCC."</div>";
		echo "<div id='ccreq'>".$CC."</div>";
		echo "<div id='ccpass'>";
		if($CC>$RECCC){
			echo "0";
		}
		else{
			echo "1";
		}
		echo "</div>";
        echo "<div id='pltype'>PERC";
        echo "</div>";
		echo "<div id='plfill'>".$RECPL."</div>";
		echo "<div id='plreq'>".$PL."</div>";
		echo "<div id='plpass'>";
		if($PL>$RECPL){
			echo "0";
		}
		else{
			echo "1";
		}
		echo "</div>";
		echo "<div id='adfill'>".$REC51."</div>";
		echo "<div id='adreq'>".$ADREQ."</div>";
		echo "<div id='hithas'>".$RECHI."</div>";
		echo "<div id='hitlim'>".$HITLIM."</div>";
		echo "<div id='psafill'>".$RECPSA."</div>";
		echo "<div id='psareq'>".$PSAREQ."</div>";
}
else{
	//echo 'ERROR!';
}
?>