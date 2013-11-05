<?php
    session_start();
	
	
	function to12hour($hour1){ 
		// 24-hour time to 12-hour time 
		return DATE("g:i A", STRTOTIME($hour1));
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
	if(!mysql_select_db("CKXU")){header('Location: /login.php');}	

	// GLOBAL SETTINGS
	$SETW = "1350px";
	
	// FETCH UNIVERSAL POST VALUES
	if(isset($_SESSION['program'])){
		$SHOW = addslashes($_SESSION['program']);
	}
	else{
		$SHOW = "NULL";
	}
	
	if(isset($_SESSION['time'])){
		$START = to24hour(addslashes($_SESSION['time']));
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
		$CALL = "CKXU";
	}
	
	// Perform Updates
	// Check for "Submit Changes"
	if(isset($_POST['changed'])){
				
			//$ERRLOG = array("Error Code" , "Error Description");
		
		// Update Header (Episode Data)
		
		// UPDATE Start Time
		if($_POST['shstat']!=""){
			$SQST = "Update Episode SET starttime=\"".addslashes($_POST['shstart'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQST)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
			else{
				// UPDATE SONGS ASSOCIATED WITH EPISODE
				$SQSO = "Update song SET starttime=\"".addslashes($_POST['shstart'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
				if(!mysql_query($SQSO)){
					//array_push($ERRLOG,mysql_errno(),mysql_error());
					echo mysql_error();
				}
				else{
					$START=	addslashes($_POST['shstart']);
				}
			}
		}
		
		// To Be Update Program Name
		// UPDATE Air Date
		if($_POST['NSHN']!=""){
			$SQSN = "Update Episode SET programname=\"".addslashes($_POST['NSHN'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQSN)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
			else{
				// UPDATE SONGS ASSOCIATED WITH EPISODE
				$SQSH = "Update song SET programname=\"".addslashes($_POST['NSHN'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
				if(!mysql_query($SQSH)){
					//array_push($ERRLOG,mysql_errno(),mysql_error());
					echo mysql_error();
				}
				else{
					$SHOW=addslashes($_POST['NSHN']);
				}
			}
		}
		 
		
		// UPDATE Air Date
		if($_POST['shdate']!=""){
			$SQSD = "Update Episode SET date=\"".addslashes($_POST['shdate'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQSD)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
			else{
				// UPDATE SONGS ASSOCIATED WITH EPISODE
				$SQOD = "Update song SET date=\"".addslashes($_POST['shdate'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
				if(!mysql_query($SQOD)){
					//array_push($ERRLOG,mysql_errno(),mysql_error());
					echo mysql_error();
				}
				else{
					$DATE=addslashes($_POST['shdate']);
				}
			}
		}
		
			// UPDATE Pre-Record Date
		if($_POST['shprec']!=""){
			$SQPR = "Update Episode SET prerecorddate=\"".addslashes($_POST['shprec'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQPR)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
		else{
			$SQPR = "Update Episode SET prerecorddate=NULL where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQPR)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
			// UPDATE Description
		if($_POST['shdesc']!=""){
			$SQDE = "Update Episode SET description=\"".addslashes($_POST['shdesc'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQDE)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
		else{
			$SQDE = "Update Episode SET description=NULL where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysql_query($SQDE)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}	
	}
		
	// Perform Selections
	$EPISQL = "select * from EPISODE where programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
	if(!$SHOWDATAAR = mysql_query($EPISQL)){
		echo "<span>SELECTION ERROR:" . mysql_errno() . " - " . mysql_error() . "</span>";
	}
	else{
		$SHOWDATA = mysql_fetch_array($SHOWDATAAR);
		$DESC = $SHOWDATA['description'];
		$PREC = $SHOWDATA['prerecorddate'];
		if( $SHOWDATA['endtime']==""){
			$FINAL="Not Finalized";
		}
		else{
			$FINAL = "Finalized at " . $SHOWDATA['endtime']; 
		}
	}
	
	//$ICC = 0;
	$EDIC = $_POST['edit'];
	$SNID = $_POST['SNID'];
	$CATS = $_POST['category'];
	$PLAY = $_POST['Playlist'];
	$SPOK = $_POST['Spoken'];
	$TIME = $_POST['times'];
	$TITL = $_POST['titles'];
	$ARTI = $_POST['artists'];
	$ALBU = $_POST['albums'];
	$COMP = $_POST['composers'];
	$CANC = $_POST['cc'];
	$HITZ = $_POST['hit'];
	$INST = $_POST['ins'];
	$LANG = $_POST['language'];
	$NOTE = $_POST['note'];
	$REMO = $_POST['remove'];
	 
	$END = count($EDIC);
	for ($i=0; $i < $END; $i++){
		$INDEX =  $EDIC[$i];
		//echo $SNID[$INDEX];
		//echo $INDEX;
		// CATEGORY
		$SQROW = "UPDATE song SET category='".$CATS[$INDEX]."' ";
		// PLAYLIST
		if($PLAY[$INDEX]!=""){
			$SQROW .= ", playlistnumber='".$PLAY[$INDEX]."' ";
		}
		else{
			$SQROW .=", playlistnumber=NULL ";
		}
		// SPOKEN
		if($SPOK[$INDEX]!=""){
			$SQROW .= ", Spoken='".$SPOK[$INDEX]."' ";
		}
		else{
			$SQROW .=", Spoken=NULL ";
		}
		// NOTE
		if($NOTE[$INDEX]!=""){
			$SQROW .= ", note='".$NOTE[$INDEX]."' ";
			
		}
		else{
			$SQROW .=", note=NULL ";
		}
		// HIT
		if(in_array($SNID[$INDEX], $HITZ))
		{
			$SQROW .= ", hit='1' ";
		}
		else{
			$SQROW .= ", hit='0' ";
		}
		// Can Con
		if(in_array($SNID[$INDEX], $CANC))
		{
			$SQROW .= ",cancon='1' ";
		}
		else{
			$SQROW .= ", cancon='0' ";
		}
		// Ins
		if(in_array($SNID[$INDEX], $INST))
		{
			$SQROW .= ", instrumental='1' ";
		}
		else{
			$SQROW .= ", instrumental='0' ";
		}
		
		//TIME 
		if($TIME[$INDEX]!=""){
			$SQROW .= " , time='".to24hour($TIME[$INDEX])."' ";
		}	
		else{
			$SQROW .= " , time=NULL ";
		}
		$SQROW .= " , title='".addslashes($TITL[$INDEX])."', artist='".addslashes($ARTI[$INDEX])."', album='".addslashes($ALBU[$INDEX])."', composer='".addslashes($COMP[$INDEX])."' ";
		$SQROW .= " where songid='".$SNID[$INDEX]."' ";
		
		if(!mysql_query($SQROW))
		{
			echo $SQROW . "<br/>";
			echo mysql_error();
		}
		else{
			//echo $SQROW . "<br/>";
			// UPDATE LANGUAGE
			$SQLAN = "Update language set languageid='".$LANG[$INDEX]."' where songid='".$SNID[$INDEX]."' ";
			if(!mysql_query($SQLAN)){
				echo mysql_error();
			}
		}	
	}
	for($ct=0;$ct<count($REMO);$ct++){
		$RMQ = "delete from song where songid='".$REMO[$ct]."' ";
		$RML = "delete from language where songid='".$REMO[$ct]."' ";
		if(!mysql_query($RMQ)){
			echo mysql_error();
		}
		else{
			if(!mysql_query($RML)){
				echo mysql_error();
			}
		}
	}
	
}
else{
	echo 'ERROR!';
}
header("location: ../logs.php");
?>