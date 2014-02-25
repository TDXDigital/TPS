<?php
    include_once "../../../../TPSBIN/functions.php";
    sec_session_start();
    $DEBUG = FALSE;
	
	function to12hour($hour1){ 
		// 24-hour time to 12-hour time 
		return DATE("g:i a", STRTOTIME($hour1));
	}
	function to24hour($hour2){
		// 12-hour time to 24-hour time 
		return DATE("H:i", STRTOTIME($hour2));
	}
	
$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
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
	}

    // GET SYSTEM SETTINGS
	$SETTA = mysql_query("SELECT * FROM station,program,genre where station.callsign = program.callsign and program.callsign = '".$CALL."' and program.programname='".$SHOW."' and program.genre=genre.genreid");
    if($DEBUG){
        echo "SELECT * FROM station,program,genre where station.callsign = program.callsign and program.callsign = '".$CALL."' and program.programname='".$SHOW."' and program.genre=genre.genreid
        ";
    }
    if(sizeof($SETTA)===1){
        $SETTINGS = mysql_fetch_array($SETTA);
    }
    else{
        die("Program Error 005");
    }
    /*
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
			die("Program Error 001 " . mysql_error());
		}
		if(!($Requirements = mysql_fetch_array($result))){
			die("Program Error 002 " . mysql_error());
		}
		$SQL2PR = "SELECT * from Program where programname=\"" . $SHOW . "\" and callsign=\"" . $CALL . "\" ";
		if(!($result2 = mysql_query($SQL2PR))){
			die("Program Error 003 " . mysql_error());
		}
		if(!($Req2 = mysql_fetch_array($result2))){
			die("Program Error 004 " . mysql_error());
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
		$SQLCOUNT = "SELECT (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and cancon='1') AS CanCon,
        (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and hit='1') AS Hits,
        (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category='51' and AdViolationFlag is NULL) AS Ads,
        (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and playlistnumber IS NOT NULL) AS Playlist,
        (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and (category like '2%' or category like '3%')) AS Songs,
        (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category='45') AS Promo,
        (Select count(*) from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category like '1%' and title like '%PSA%' ) AS PSA,
        (Select round(((Playlist / Songs)*100),1)) AS Playlist_Percent,
        (Select round(((CanCon / Songs)*100),1)) AS CanCon_Percent";
		if( $DEBUG){
            echo $SQLCOUNT;
        }
        if(!$result = mysql_query($SQLCOUNT)){
		    die("Program Error 006: " . mysql_error());
		}
		if(!$REC = mysql_fetch_array($result)){
		    die("Program Errror 007: ".mysql_error());
		}
		/*
		// COUNT HITS
		$SQLCOUNTHI = "Select count from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and hit='1' ";
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
		*/
		$resultPSA = mysql_query($SQLCOUNTPSA);
		$resultPROMO = mysql_query($SQLCOUNTPROMO);
		$RECPSA = mysql_num_rows($resultPROMO);
		$RECPSA += mysql_num_rows($resultPSA);
		
		$ADREQ = ceil($SETTINGS['ST_ADSH'] * $SETTINGS['length'] / 60);
		$PSAREQ = ceil($SETTINGS['ST_PSAH'] * $SETTINGS['length'] / 60);
		$HITLIM = $Req2['HitLimit'];
		
		// NEW STYLE CODE
		
		$ccfill = 0;
		/*$ccf = "SELECT count(songid) from song where callsign='".$CALL."' and starttime='".$START."' and programname='".$SHOW."' and date='".$DATE."' and cancon='1'";
		$resultarrcf = mysql_query($ccf);
		$ccfarr = mysql_fetch_array($resultarrcf);*/
        if($SETTINGS['CCType']==0){
            echo "<div id='ccfill'>".$REC['CanCon_Percent']."</div>";
            echo "<div id='ccreq'>";
            echo ($SETTINGS['canconperc']*100)."%";
            echo "</div>";
            echo "<div id='ccpass'>";
		    if(($SETTINGS['canconperc']*100)>$REC['CanCon_Percent']){
			    echo "0";
		    }
		    else{
			    echo "1";
		    }
		    echo "</div>";
        }
        else{
            echo "<div id='ccfill'>".$REC['CanCon']."</div>";
            echo "<div id='ccreq'>".$CC."</div>";
            echo "<div id='ccpass'>";
		    if($CC>$REC['CanCon']){
			    echo "0";
		    }
		    else{
			    echo "1";
		    }
		    echo "</div>";
        }
		/*
		echo "<div id='ccfill'>".$REC['CanCon']."</div>";
		echo "<div id='ccreq'>".$CC."</div>";
		echo "<div id='ccpass'>";
		if($CC>$REC['CanCon']){
			echo "0";
		}
		else{
			echo "1";
		}
		echo "</div>";*/
        echo "<div id='pltype'>";
        echo $SETTINGS['PlType'];
        echo "</div>";
        echo "<div id='cctype'>";
        echo $SETTINGS['CCType'];
        echo "</div>";
		
		
        if($SETTINGS['PlType']==0){
            echo "<div id='plfill'>".$REC['Playlist_Percent']."</div>";
            echo "<div id='plreq'>";
            echo ($SETTINGS['playlistperc']*100)."%";
            echo "</div>";
            echo "<div id='plpass'>";
		    if((($SETTINGS['playlistperc'])*100)>$REC['Playlist_Perc']){
			    echo "0";
		    }
		    else{
			    echo "1";
		    }
		    echo "</div>";
        }
        else{
            echo "<div id='plfill'>".$REC['Playlist']."</div>";
            echo "<div id='plreq'>".$PL."</div>";
            echo "<div id='plpass'>";
		    if($PL>$REC['Playlist']){
			    echo "0";
		    }
		    else{
			    echo "1";
		    }
		    echo "</div>";
        }
		
		echo "<div id='adreq'>".$ADREQ."</div>";
		echo "<div id='adfill'>".$REC['Ads']."</div>";
		echo "<div id='hithas'>".$REC['Hits']."</div>";
		echo "<div id='hitlim'>".$HITLIM."</div>";
		echo "<div id='psafill'>".$REC['PSA']."</div>";
		echo "<div id='psareq'>".$PSAREQ."</div>";
}
else{
	//echo 'ERROR!';
}
?>