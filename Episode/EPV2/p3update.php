<?php
    session_start();
	$DEBUG = FALSE;
	
$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../../login.php');}	

	// GLOBAL SETTINGS
	$SETW = "1350px";
	
	// FETCH UNIVERSAL POST VALUES
	if(isset($_POST['program'])){
		$SHOW = addslashes($_POST['program']);
	}
	else{
		$SHOW = "NULL";
	}
	
	if(isset($_POST['user_time'])){
		$START = addslashes($_POST['user_time']);
	}
	else{
		$START = "00:00:00";
	}
	
	if(isset($_POST['user_date'])){
		$DATE = addslashes($_POST['user_date']);
	}
	else{
		$DATE = date("Y-m-d");
	}
	
	if(isset($_POST['callsign'])){
		$CALL = addslashes($_POST['callsign']);
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
			$SQROW .= " , time='".$TIME[$INDEX]."' ";
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
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../altstyle.css" />
    <link rel="stylesheet" type="text/css" href="../../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.min.css" />
    <script src="../../js/jquery/js/jquery-2.0.3.min.js"></script>
    <script src="../../js/jquery/js/jquery-ui-1.10.0.custom.min.js"></script>
<title>Log Editor</title>
</head>
<html>
<body>
	<script type="text/javascript">
		function SetRem(chk, ID , ROW , COUNT) {
			if(chk == true){
				document.getElementById(ID).style.background = 'red';	
			}
			else{
				//alert('UNCHECK')
				if(COUNT%2){
					document.getElementById(ID).style.background = '#DAFFFF';//'#F9F9AA';
				}
				else{
					document.getElementById(ID).style.background = 'white';
				}
			}
		}
		
		function SetEdit(Row){
			//alert(Row);
			//document.forms['general'].Row.checked="true";
			document.getElementById(Row).checked="true";
		}
		
		function SetNote(ELID,EDI){
			//var VAL = document.getElementById(ELID).value;
			//alert(document.getElementById(ELID).value)
			document.getElementById(EDI).checked="true";
			var NOTE = prompt("Notes for individual song (90 char Max)", document.getElementById(ELID).value );
			if(NOTE != null){
				document.getElementById(ELID).value = NOTE;
			}			
		}
	</script>
	<div class="topbar">
           USER: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header" style="width: <?php echo $SETW ?>">
		<a href="#"><img src="../../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top" style="width: <?php echo $SETW ?>">
		<table><tr><td width="200px"><span style="font-size: 25px;">Update/Edit Log</span></td><td width="100px"></td><td width="300px"><span>Sponsor:<?php
			$SELSPON = "SELECT * FROM program where programname='".$SHOW."' and callsign='".$CALL."' ";
			$SEL = mysql_query($SELSPON);
			$vars = mysql_fetch_array($SEL);
			if($vars['SponsId']!="")
			{
				echo $vars['SponsId'];
			}
			else{
				echo " N/A ";
			}
		?>
			</span></td><td width="300px">
				<span> Show Classification: <?php
					echo $vars['genre'];
				?></span>
			</td><td style=" min-width='225px'">
				<?php
				$getgen = "select * from genre where genreid='" . $vars['genre'] . "' ";
				$reqar = mysql_query($getgen);
				$req = mysql_fetch_array($reqar);
				if($req['CCType']=='0'){
                    $SQL_CC_COUNT = "SELECT 
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%' and cancon='1') AS CC_Num,
                    (SELECT round(((CC_Num / Total)*100),2)) AS Percent";
                    if(!$CC_PER_RES = mysql_query($SQL_CC_COUNT)){
                        echo "<span class='ui-state-highlight ui-corner-all'>".mysql_error()."</span>";
                        //break;
                    }
                    else{
                        $PER_CC = mysql_fetch_array($CC_PER_RES);
                        echo "<span ";
                        if(floatval($PER_CC['Percent']) < floatval($req['canconperc'])*100){
                            echo "class=\"ui-state-error ui-corner-all\" >";
                        }
                        else{
                            echo ">";
                        }
                        echo "Canadian Content Required</span><br/><span><strong>";
				        echo $PER_CC['Percent']." / ".(floatval($req['canconperc'])*100)."%</strong>";
                        if($DEBUG){
                            echo "[".$PER_CC['CC_Num']."/".$PER_CC['Total']."]";
                        }
                    }
                }
                else if($req['CCType']=='1'){
                    if($vars['CCX']=='-1'){
						$CCR = ceil($req['cancon'] * $vars['length'] / 60);
					}
					else{
						$CCR = ceil($vars['CCX'] * $vars['length'] / 60);
					}
                    $SQLCOUNTCC = "Select count(songid) AS EnteredCC from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and cancon='1' ";
				    $resultCC = mysql_query($SQLCOUNTCC);
                    $CC_VARS = mysql_fetch_array($resultCC);
                    echo "<span ";
                    if(floatval($CC_VARS['EnteredCC']) < floatval($CCR)){
                        echo "class=\"ui-state-error ui-corner-all\" >";
                    }
                    else{
                        echo ">";
                    }
                    echo "Canadian Content Required </span><br/></span>";
				    echo $CC_VARS['EnteredCC']."/".$CCR;
				}
					?></span>
			</td><td width="225px">
				<?php
                if($req['PlType']=='0'){
                    $SQL_PL_COUNT = "SELECT 
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%' and Playlistnumber IS NOT NULL) AS Count,
                    (SELECT round(((Count / Total)*100),2)) AS Percent";
                    if(!$PL_PER_RES = mysql_query($SQL_PL_COUNT)){
                        echo "<span class='ui-state-highlight ui-corner-all'>".mysql_error()."</span>";
                        //break;
                    }
                    else{
                        $PER_PL = mysql_fetch_array($PL_PER_RES);
                        echo "<span ";
                        if(floatval($PER_PL['Percent']) < floatval($req['playlistperc'])*100){
                            echo "class=\"ui-state-error ui-corner-all\" >";
                        }
                        else{
                            echo ">";
                        }
                        echo "Playlist Required </span><br/><span>";
				        echo "<strong>".$PER_PL['Percent']." / ".(floatval($req['playlistperc'])*100)."%</strong>";
                        if($DEBUG){
                            echo "[".$PER_PL['Count']."/".$PER_PL['Total']."]";
                        }
                    }
                }
                else if($req['PlType']=='1'){
						if($vars['PLX']=='-1'){
							$PLR = ceil($req['playlist'] * $vars['length'] / 60);
						}
						else{
							$PLR = ceil($vars['PLX'] * $vars['length'] / 60);
						}
				
				    $SQLCOUNTPL = "Select count(songid) AS EnteredPL from SONG where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and playlistnumber is not null ";
				    $resultPL = mysql_query($SQLCOUNTPL);
                    $PL_VARS = mysql_fetch_array($resultPL);
				    //$PL = mysql_num_rows($resultPL);	
				    echo "<span ";
                    if(floatval($PL_VARS['EnteredPL']) < floatval($PLR)){
                        echo "class=\"ui-state-error ui-corner-all\" >";
                    }
                    else{
                        echo ">";
                    }
                    echo "Playlist Required</span><br/><span>";
				    echo $PL_VARS['EnteredPL']."/".$PLR;
                }
                
				
					?></span>
			</td></tr></table>
		<table style="text-align: center;">
			<tr>
				<th>Show Name</th>
				<th>Station</th>
				<th>Start Time</th>
				<th>Air Date</th>
				<th>Prerecord<input type="button" value="clear" onclick="document.forms['general'].shprec.value=''; return false;"/></th>
				<!--<th>Show Description</th>-->
				<th>Spoken Time</th>
				<th>Finalized</th>
			</tr>
			<tr>
				<form method="POST" name="general" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
				<td><input type="hidden" title="Show Name" name="shname" maxlength="90" readonly="true" value="<?php echo $SHOW; ?>"/>
					<select required title="Show Name" name="NSHN"><?php 
					$SHOWSQL = "SELECT * FROM program WHERE active='1' and callsign='".$CALL."' ";
					$SHOWARRAY = mysql_query($SHOWSQL);
					while($OPSH = mysql_fetch_array($SHOWARRAY)){
						echo "<option value='".$OPSH['programname'];
						if(addslashes($OPSH['programname'])==$SHOW){
							echo "' selected style=\"background-color:darkgreen; color:white;\" ";
						}
						else{
							echo "' ";
						}
						echo ">".$OPSH['programname']."</option>";
					}
					?>"</select></td>
				<td><input type="text" required title="Station Name" maxlength="5" size="5" name="shstat" readonly="true" value="<?php echo $CALL; ?>"/></td>
				<td><input type="time" required title="Start Time" name="shstart" onmousewheel="javascript: return false" value="<?php echo $START; ?>"/></td>
				<td><input type="date" required title="Show Performance (Air Date) Date" name="shdate" value="<?php echo $DATE; ?>"/></td>
				<td><input type="date" title="Date Show Prerecorded On" name="shprec" value="<?php echo $PREC; ?>"/></td>
				
				<td><input type="text" title="Total Calcualted Time Spoken" name="shspkn" value="Auto Calculate" disabled readonly="readonly"/></td>
				<td><input type="text" title="Specifies if program is finalized" value="<?php echo $FINAL; ?>" readonly="true"/></td>
			</tr>
            <tr>
                <td colspan="7"><input type="text" title="Show Description" style="width: 1340px;" placeholder="Program Description (Recommended)" name="shdesc" value="<?php echo $DESC; ?>"/></td>
            </tr>
		</table>
	</div>
	<?php
		try{
			include "../../TPSBIN/XML/Emergency.php";
		}
		catch (Exception $e)
		{
			Echo "<span> Error getting Emergency Alerts</span>";
		}
	?>
	<div id="content" style="width: <?php echo $SETW ?>">
		<table border="0" class="tablecss">
            <tbody>
			<!--<tr><td colspan="100%"><h2>*** Work in Progress ***</h2></td></tr>-->
			
				<th width="50px">
					<span title="Changed Value" class="ui-icon ui-icon-transferthick-e-w"></span>
				</th>
				<th width="200px">
					Type
				</th>
				<th width="75px">
					Playlist
				</th>
				<th width="75px">
					Spoken
				</th>
				<th width="75px">
					Time
				</th>
				<th width="150px">
					Title
				</th>
				<th width="150px">
					Artist
				</th>
				<th width="150px">
					Album
				</th>
				<th width="150px">
					Composer
				</th>
				<th width="25px">CC</th>
				<th width="25px">Hit</th>
				<th width="25px">Ins</th>
				<th width="75px">Language</th>
				<th><span title="Note" class="ui-icon ui-icon-tag"></span></th>
				<th><span title="Delete Record" class="ui-icon ui-icon-trash"></span></th>
			</tr>
            </tbody>
			

<?php
	$FETSON = "SELECT * from SONG where programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL ."' order by time ".$vars['displayorder'];
	//echo $FETSON; // sql qUEREY pRINT oUT
	
	//echo $FETSON; //DEBUG USE ONLY
	if(!$SONRES = mysql_query($FETSON))
	{
		echo "FETCH ERROR: Could not Fetch Songs performed, Server Returned (".mysql_errno().": ".mysql_error().") <br/><br/>SQL:";
		echo $FETSON;
	}
	else{
		$CONT = 0;
		while($SONGS = mysql_fetch_array($SONRES)){
			echo "<tr id=\"" . $SONGS['songid'];
			if($CONT%2){
				echo "\" style=\"background-color: #DAFFFF;";
			}
			echo "\" ><td><input type=\"text\" value=\"".$SONGS['songid']."\" hidden name=\"SNID[]\" /><input type=\"checkbox\" name=\"edit[]\" id=\"EDI".$CONT."\" value=\"" . $CONT . "\" title=\"Checked if row is modified\" onclick=\"javascript:return false\" /></td>";
			//echo "<td><input type=\"number\" max=\"53\" min=\"11\" name=\"Category[]\" value=\"" . $SONGS['category'] . "\" /></td>";
			// CATEGORY HANDLER [TANSFERED]
			$OPT = "<td><select name=\"category[]\" "; 
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				$OPT .= " disabled=\"disabled\" ><option>51 , Commercial</option></select><input type=\"hidden\" name=\"category[]\" value=\"".$SONGS['category']."\" /></td>";
				echo $OPT;
			}
			else{
				$OPT .= "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\">
				";
	                                /*
	                                if($list['category']<10){
		                                $OPT .= "<OPTION VALUE=\"5\"";
		                                if($list['category']=="5"){$OPT.="selected=\"selected\"";}
		                                $OPT.= ">5, Advertisement</option><OPTION VALUE=\"4\"";
		                                if($list['category']=="4"){$OPT.="selected=\"selected\"";}
		                                $OPT.= ">4, Musical Production</option><OPTION VALUE=\"3\"";
		                                if($list['category']=="3"){$OPT.="selected=\"selected\"";}
		                                $OPT.= ">3, Special Interest</option><OPTION VALUE=\"2\"";
		                                if($list['category']=="2"){$OPT.="selected=\"selected\"";}
		                                $OPT.= ">2, Popular Music</option><OPTION VALUE=\"1\"";
		                                if($list['category']=="1"){$OPT.="selected=\"selected\"";}
		                                $OPT.= ">1, Spoken Word</option>";
									}
									*/
									// OPTIONS FOR SUB CATEGORIES
									//else{
									$OPT .= "<OPTION value=\"53\"";
									if($SONGS['category']=="53")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">53, Sponsored Promotion</option>";
									
									$OPT .= "<OPTION value=\"52\"";
									if($SONGS['category']=="52")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">52, Sponsor Indentification</OPTION>";
									
									$OPT .= "<OPTION value=\"51\"";
									if($SONGS['category']=="51")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">51, Commercial</option>";
									
									$OPT .= "<OPTION value=\"45\"";
									if($SONGS['category']=="45")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= "> 45, Show Promo</option>";
									
									$OPT .= "<OPTION value=\"44\"";
									if($SONGS['category']=="44")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">44, Programmer/Show ID</option>";
									
									
									$OPT .= "<OPTION value=\"43\"";
									if($SONGS['category']=="43")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">43, Station ID</option>";
									
									$OPT .= "<OPTION value=\"42\"";
									if($SONGS['category']=="42")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">42, Tech Test</option>";
									
									$OPT .= "<OPTION value=\"41\"";
									if($SONGS['category']=="41")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">41, Themes</option>";
									
									/*$OPT .= "<OPTION value=\"40\"";
									if($list['category']=="40")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">40, Musical Production</option>";
									*/
									
									// CATEGORY 3 ---------------------------------------
									$OPT .= "<option value=\"36\"";
									if($SONGS['category']=="36")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">36, Experimental</option>";
									
	                                $OPT .= "<option value=\"35\"";
									if($SONGS['category']=="35")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">35, NonClassical Religious</option>";
									
	                                $OPT .= "<option value=\"34\"";
									if($SONGS['category']=="34")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">34, Jazz and Blues</option>";
									
	                                $OPT .= "<option value=\"33\"";
									if($SONGS['category']=="33")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">33, World/International</option>";
									
	                                $OPT .= "<option value=\"32\"";
									if($SONGS['category']=="32")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">32, Folk</option>";
									
	                                $OPT .= "<option value=\"31\"";
									if($SONGS['category']=="31")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">31, Concert</option>";
									
									// CATEGORY 2 ---------------------------------------
									if($SONGS['category']=="3"){
										$OPT .= "<OPTION value=\"3\" selected=\"true\" >3, Special Interest</option>";
									}
									
									$OPT .= "<OPTION value=\"24\"";
									if($SONGS['category']=="24")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">24, Easy Listening</option>";
									
									$OPT .= "<OPTION value=\"23\"";
									if($SONGS['category']=="23")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">23, Acoustic</option>";
									
									$OPT .= "<OPTION value=\"22\"";
									if($SONGS['category']=="22")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">22, Country</option>";
									
									$OPT .= "<OPTION value=\"21\"";
									if($SONGS['category']=="21")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">21, Pop, Rock and Dance</option>";
									
									if($SONGS['category']=="2"){
										$OPT .= "<OPTION value=\"2\" selected=\"true\" >2, Popular Music</option>";
									}
									
									$OPT .= "<OPTION value=\"12\"";
									if($SONGS['category']=="12")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">12, Spoken Word Other</option>";
									
									$OPT .= "<OPTION value=\"11\"";
									if($SONGS['category']=="11")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">11, News</option>";
									
									$OPT .= "</select>";
									//}
									/*
									 *  <option value="53">53, Sponsored Promotion</option>
	                                 *  <OPTION value="52">52, Sponsor Indentification</OPTION>
	                                 *  <OPTION VALUE="51">51, Commercial</OPTION>
	                                 *  <OPTION VALUE="40">40, Musical Production</option>
	                                 *  <OPTION VALUE="30">30, Special Interest</option>
	                                 *  <OPTION VALUE="20" selected="selected">20, Popular Music</option>
	                                 *  <option value="12">12, Spoken word other</option>
	                                 *  <OPTION VALUE="11">11, News</option>
									 */
	                                echo $OPT . "</td>";
	                  }
			
			echo "<td><input onchange=\"SetEdit('EDI".$CONT."')\" onmousewheel=\"javascript: return false\" onclick=\"SetEdit('EDI".$CONT."')\" type=\"number\" min=\"1\" size=\"6\" style=\"width:65px;\" name=\"Playlist[]\" value=\"" . $SONGS['playlistnumber'] . "\" /></td>";
			echo "<td><input onchange=\"SetEdit('EDI".$CONT."')\" onmousewheel=\"javascript: return false\" onclick=\"SetEdit('EDI".$CONT."')\" type=\"number\" min=\"0\" size=\"6\" style=\"width:65px;\" name=\"Spoken[]\" step=\"0.25\" value=\"" . $SONGS['Spoken'] . "\" /></td>";
			echo "<td><input onchange=\"SetEdit('EDI".$CONT."')\" onmousewheel=\"javascript: return false\" onclick=\"SetEdit('EDI".$CONT."')\" type=\"time\" name=\"times[]\" value=\"" . $SONGS['time']."\" /> </td>";
			echo "<td><input ";
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				echo " readonly=\"readonly\" ";
			}
			echo "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\" required type=\"text\" name=\"titles[]\" value=\"" . $SONGS['title'] . "\" maxlength=\"90\" /> </td>";
			echo "<td><input ";
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				echo " readonly=\"readonly\" ";
			}
			echo "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"artists[]\" value=\"" . $SONGS['artist'] . "\" maxlength=\"90\" /> </td>";
			echo "<td><input ";
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				echo " readonly=\"readonly\" ";
			}
			echo "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"albums[]\" value=\"" . $SONGS['album'] . "\" maxlength=\"90\" /> </td>";
			echo "<td><input ";
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				echo " readonly=\"readonly\" ";
			}
			echo "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"composers[]\" value=\"" . $SONGS['composer'] . "\" maxlength=\"90\" /> </td>";
			echo "<input onclick=\"SetEdit('EDI".$CONT.",EDI".$CONT."')\" type=\"text\" hidden name=\"note[]\" id=\"NTI".$CONT."\" value='".$SONGS['note']."' />";
			echo "<td><input onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"cc[]\" value='".$SONGS['songid']."' ";
			
			if( $SONGS['cancon'] == "1"){
				echo " checked ";
			}
			echo "/></td> ";
			echo "<td><input onChange=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"hit[]\" value='".$SONGS['songid']."' ";
			if( $SONGS['hit'] == "1"){
				echo " checked ";
			}
			echo "/></td> ";
			echo "<td><input onChange=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"ins[]\" value='".$SONGS['songid']."' ";
			if( $SONGS['instrumental'] == "1"){
				echo " checked ";
			}
			echo "/></td> ";
			$LANS = mysql_fetch_array(mysql_query("SELECT languageid from language where songid=\"" . $SONGS['songid'] . "\" "));
			echo "<td><input onchange=\"SetEdit('EDI".$CONT."')\" onclick=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"language[]\" value=\"". $LANS['languageid'] . "\" size=\"10\" maxlength=\"40\" /></td>";
			echo "<td><input type=\"button\" value=\"";
				if($SONGS['note']!=''){
					echo 'Y';
				}
				else{
					echo "N";
				}
			echo "\" onclick=\"SetNote('NTI".$CONT."','EDI".$CONT."')\" /></td>";
			echo "<td><input type=\"checkbox\" value=\"".$SONGS['songid']."\" id=\"checkbox".$SONGS['songid']."\" name=\"remove[]\" onClick=\"SetRem(this.checked,".$SONGS['songid']." ,checkbox".$SONGS['songid'].",".$CONT.")\" /></td>";			
			echo "</tr>
			";
			++$CONT;
		}
		
	}
?>
</table>	
		</div>
	<div id="foot" style="width: <?php echo $SETW ?>">
		<table>
			<tr>
				<td>
					<input name="changed" value="true" hidden="true" />
						<input type="text" hidden name="changed" value="TRUE"/>
						<input type="text" hidden name="callsign" value=<?php echo "\"" . $CALL . "\"" ?> />
	            		<input type="text" hidden name="program" value=<?php echo "\"" . stripslashes($SHOW) . "\"" ?> />
	            		<input type="text" hidden name="user_date" value=<?php echo "\"" . $DATE . "\"" ?> />
	            		<input type="text" hidden name="user_time" value=<?php echo "\"" . $START . "\"" ?> />
					<input type="submit" value="Submit Changes"></form></td><td>
					<form action="../p2insertEP.php" method="POST">
						<input type="text" hidden name="callsign" value=<?php echo "\"" . $CALL . "\"" ?> />
	            		<input type="text" hidden name="program" value=<?php echo "\"" . stripslashes($SHOW) . "\"" ?> />
	            		<input type="text" hidden name="user_date" value=<?php echo "\"" . $DATE . "\"" ?> />
	            		<input type="text" hidden name="user_time" value=<?php echo "\"" . $START . "\"" ?> />
	            		<input type="submit" value="Return to Addition"/>
	            	</form></td><td>
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
						<input type="text" hidden name="callsign" value=<?php echo "\"" . $CALL . "\"" ?> />
	            		<input type="text" hidden name="program" value=<?php echo "\"" . stripslashes($SHOW) . "\"" ?> />
	            		<input type="text" hidden name="user_date" value=<?php echo "\"" . $DATE . "\"" ?> />
	            		<input type="text" hidden name="user_time" value=<?php echo "\"" . $START . "\"" ?> />
						<input type="submit" value="Reset" />
					</form></td><td>
				<!--<form method="POST" action="/masterpage.php"><input type="submit" value="Menu"/></form>-->
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>
