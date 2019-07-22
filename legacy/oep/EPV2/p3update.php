<?php
    session_start();
    date_default_timezone_set($_SESSION['TimeZone']);
    $DEBUG = filter_input(INPUT_POST,'debug',FILTER_SANITIZE_NUMBER_INT)?:FALSE;
    if(!$DEBUG){
        error_reporting(E_ERROR);
    }

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysqli_select_db($con, $_SESSION['DBNAME'])){header('Location: ../../login.php');}

	// GLOBAL SETTINGS
	$SETW = "1350px";

	// FETCH UNIVERSAL POST VALUES
	if(isset($_POST['program'])){
		$SHOW = $_POST['program'];
                if ( urlencode(urldecode($SHOW)) === $SHOW){
                    //data is urlencoded
                    $SHOW = addslashes(urldecode($SHOW));
                } else {
                    //data is NOT urlencoded
                    $SHOW = addslashes($SHOW);
                }
	}
        else if(isset($_GET['program'])){
		$SHOW = $_GET['program'];
                if ( urlencode(urldecode($SHOW)) === $SHOW){
                    //data is urlencoded
                    $SHOW = addslashes(urldecode($SHOW));
                } else {
                    //data is NOT urlencoded
                    $SHOW = addslashes($SHOW);
                }
	}
	else{
		$SHOW = "NULL";
	}

	if(isset($_POST['user_time'])){
		$START = addslashes($_POST['user_time']);
	}
        else if(isset($_GET['user_time'])){
		$START = addslashes($_GET['user_time']);
	}
	else{
		$START = "00:00:00";
	}

	if(isset($_POST['user_date'])){
		$DATE = addslashes($_POST['user_date']);
	}
        else if(isset($_GET['user_date'])){
		$DATE = addslashes($_GET['user_date']);
	}
	else{
		$DATE = date("Y-m-d");
	}

	if(isset($_POST['callsign'])){
		$CALL = addslashes($_POST['callsign']);
	}
        else if(isset($_GET['callsign'])){
		$CALL = addslashes($_GET['callsign']);
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
			$SQST = "Update `episode` SET starttime=\"".addslashes($_POST['shstart'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQST)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
			else{
				// UPDATE SONGS ASSOCIATED WITH EPISODE
				$SQSO = "Update `song` SET starttime=\"".addslashes($_POST['shstart'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
				if(!mysqli_query($con, $SQSO)){
					//array_push($ERRLOG,mysql_errno(),mysql_error());
					echo mysql_error();
				}
				else{
					$START=	addslashes($_POST['shstart']);
				}
			}
		}

		// To Be Update Program Name (does not work...)
		// UPDATE Air Date
		/*if($_POST['NSHN']!=""){
			$SQSN = "Update Episode SET programname=\"".addslashes($_POST['NSHN'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQSN)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
			else{
				// UPDATE SONGS ASSOCIATED WITH EPISODE
				$SQSH = "Update song SET programname=\"".addslashes($_POST['NSHN'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
				if(!mysqli_query($con, $SQSH)){
					//array_push($ERRLOG,mysql_errno(),mysql_error());
					echo mysql_error();
				}
				else{
					$SHOW=addslashes($_POST['NSHN']);
				}
			}
		}*/
		 if($_POST['NSHN']!=""){
		 	$SQNS = "Update `episode`, song SET episode.programname=\"".addslashes($_POST['NSHN'])."\" , ";
		 }

		// UPDATE Air Date
		if($_POST['shdate']!=""){
			$SQSD = "Update `episode` SET date=\"".addslashes($_POST['shdate'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQSD)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
			else{
				// UPDATE SONGS ASSOCIATED WITH EPISODE
				$SQOD = "Update `song` SET date=\"".addslashes($_POST['shdate'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
				if(!mysqli_query($con, $SQOD)){
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
			$SQPR = "Update `episode` SET prerecorddate=\"".addslashes($_POST['shprec'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQPR)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
		else{
			$SQPR = "Update `episode` SET prerecorddate=NULL where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQPR)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
			// UPDATE Description
		if($_POST['shdesc']!=""){
			$SQDE = "Update `episode` SET description=\"".addslashes($_POST['shdesc'])."\" where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQDE)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
		else{
			$SQDE = "Update `episode` SET description=NULL where programname=\"" . $SHOW . "\" and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
			if(!mysqli_query($con, $SQDE)){
				//array_push($ERRLOG,mysql_errno(),mysql_error());
				echo mysql_error();
			}
		}
	}

	// Perform Selections
	$EPISQL = "select * from `episode` where programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL . "'";
	if(!$SHOWDATAAR = mysqli_query($con, $EPISQL)){
		echo "<span>SELECTION ERROR:" . mysql_errno() . " - " . mysql_error() . "</span>";
	}
	else{
		$SHOWDATA = mysqli_fetch_array($SHOWDATAAR);
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
	$EDIC = $_POST['edit'];//filter_input(INPUT_POST,'edit',FILTER_SANITIZE_NUMBER_INT);//
	$SNID = $_POST['SNID'];//filter_input(INPUT_POST,'SNID',FILTER_SANITIZE_NUMBER_INT);//
	$CATS = $_POST['category'];//filter_input(INPUT_POST,'category',FILTER_SANITIZE_STRING);//
	$PLAY = $_POST['Playlist'];//filter_input(INPUT_POST,'Playlist',FILTER_SANITIZE_STRING);//
	$SPOK = $_POST['Spoken'];//filter_input(INPUT_POST,'Spoken',FILTER_SANITIZE_NUMBER_FLOAT);//
	$TIME = $_POST['times'];//filter_input(INPUT_POST,'times',FILTER_SANITIZE_STRIPPED);//
	$TITL = $_POST['titles'];//filter_input(INPUT_POST,'titles',FILTER_SANITIZE_STRING);//
	$ARTI = $_POST['artists'];//filter_input(INPUT_POST,'artists',FILTER_SANITIZE_STRING);//
	$ALBU = $_POST['albums'];//filter_input(INPUT_POST,'albums',FILTER_SANITIZE_STRING);//
	$COMP = $_POST['composers'];//filter_input(INPUT_POST,'composers',FILTER_SANITIZE_STRING);//
	$CANC = $_POST['cc'];//filter_input(INPUT_POST,'cc',FILTER_SANITIZE_NUMBER_INT);//
	$HITZ = $_POST['hit'];//filter_input(INPUT_POST,'hit',FILTER_SANITIZE_NUMBER_INT);//
	$INST = $_POST['ins'];//filter_input(INPUT_POST,'ins',FILTER_SANITIZE_NUMBER_INT);//
	$FINI = $_POST['complete'];//filter_input(INPUT_POST,'complete',FILTER_SANITIZE_NUMBER_INT);//
	$TYPE = $_POST['type'];//filter_input(INPUT_POST,'type',FILTER_SANITIZE_STRING);//
	$LANG = $_POST['language'];//filter_input(INPUT_POST,'languages',FILTER_SANITIZE_STRING);//
	$NOTE = $_POST['note'];//filter_input(INPUT_POST,'note',FILTER_SANITIZE_STRING);//
	$REMO = $_POST['remove'];//filter_input(INPUT_POST,'remove',FILTER_SANITIZE_NUMBER_INT);//

	$END = count($EDIC);
	for ($i=0; $i < $END; $i++){
		$INDEX =  $EDIC[$i];
		//echo $SNID[$INDEX];
		//echo $INDEX;
		// CATEGORY
		$SQROW = "UPDATE song SET category='".$CATS[$INDEX]."' ";
		// PLAYLIST
		if($PLAY[$INDEX]!=""){
			$SQROW .= ", playlistnumber='".addslashes($PLAY[$INDEX])."' ";
		}
		else{
			$SQROW .=", playlistnumber=NULL ";
		}
		// SPOKEN
		if($SPOK[$INDEX]!=""){
			$SQROW .= ", Spoken='".addslashes($SPOK[$INDEX])."' ";
		}
		else{
			$SQROW .=", Spoken=NULL ";
		}
		// NOTE
		if($NOTE[$INDEX]!=""){
			$SQROW .= ", note='".addslashes($NOTE[$INDEX])."' ";

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

		if($TYPE[$INDEX]!=""){
			$SQROW .= " , type='".addslashes($TYPE[$INDEX])."' ";
		}
		else{
			$SQROW .= " , type='NA'";
		}

		//TIME
		if($TIME[$INDEX]!=""){
			$SQROW .= " , time='".addslashes($TIME[$INDEX])."' ";
		}
		else{
			$SQROW .= " , time=NULL ";
		}
		$SQROW .= " , title='".addslashes($TITL[$INDEX])."', artist='".addslashes($ARTI[$INDEX])."', album='".addslashes($ALBU[$INDEX])."', composer='".addslashes($COMP[$INDEX])."' ";
		$SQROW .= " where songid='".$SNID[$INDEX]."' ";

		if(!mysqli_query($con, $SQROW))
		{
			echo $SQROW . "<br/>";
			echo mysql_error();
		}
		else{
			//echo $SQROW . "<br/>";
			// UPDATE LANGUAGE
			$SQLAN = "Update `language` set languageid='".$LANG[$INDEX]."' where songid='".$SNID[$INDEX]."' ";
			if(!mysqli_query($con, $SQLAN)){
				echo mysql_error();
			}
		}
	}
	for($ct=0;$ct<count($REMO);$ct++){
		$RMQ = "delete from `song` where songid='".$REMO[$ct]."' ";
		$RML = "delete from `language` where songid='".$REMO[$ct]."' ";
        $RMT = "delete from `trafficaudit` where songid='".$REMO[$ct]."' ";
		if(!mysqli_query($con, $RMQ)){
			echo mysql_error();
		}
		else{
			if(!mysqli_query($con, $RML)){
				echo mysql_error();
			}
            else{
                $adid="select `advertid` from trafficaudit where songid='".$REMO[$ct]."'";
                //echo $adid;
                if($RESU_ADID=mysqli_query($con, $adid)){
                    $advert = mysqli_fetch_array($RESU_ADID);
                    if(mysql_num_rows($RESU_ADID)>0){
                        if(!mysqli_query($con, "Update `adverts` set Playcount=Playcount-1 where AdId='".$advert['advertid']."'")){
                            echo mysql_error();
                            error_log(mysql_error());
                            //echo "ERROR";
                        }
                        else{
                            if(!mysqli_query($con, $RMT)){
                                echo mysql_error();
                            }
                            else{
                                //echo $RMT." --- ".$advert['AdId'];
                            }
                        }
                    }
                    else{
                        //echo "Not > 0";
                    }
                }
                else{
                    echo mysql_error();
                }
                //echo $RMT." -+- ".$advert['AdId'];
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
<link rel="stylesheet" type="text/css" href="../../../css/altstyle.css" />
    <script src="../../../js/jquery/js/jquery-2.1.1.min.js"></script>
    <script src="../../../TPSBIN/JS/Control/Device.js"></script>
    <script src="../../../js/jquery/js/jquery-ui-1.11.0/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../../../TPSBIN/JS/GLOBAL/Utilities.js"></script>
    <link rel="stylesheet" href="../../../js/jquery/css/ui-lightness/jquery-ui-1.10.0.custom.min.css"/>
    <style>
        .ui-autocomplete-loading {
            background: white url('../../../images/GIF/ajax-loader3.gif') right center no-repeat;
          }
    </style>
<title>Log Editor</title>
</head>
<html>
<body>

	<div class="topbar">
           USER: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header" style="width: <?php echo $SETW ?>">
		<a href="#"><img src="../../../<?php echo $_SESSION['logo'];?>" alt="Logo" /></a>
	</div>
	<div id="top" style="width: <?php echo $SETW ?>">
		<table><tr><td style="width: 200px"><span style="font-size: 25px;">Update/Edit Log</span></td><td style="width: 100px"></td><td style="width: 300px"><span>Sponsor:<?php
			$SELSPON = "SELECT * FROM `program` where programname='".$SHOW."' and callsign='".$CALL."' ";
			$SEL = mysqli_query($con, $SELSPON);
			$vars = mysqli_fetch_array($SEL);
			if($vars['SponsId']!="")
			{
				echo $vars['SponsId'];
			}
			else{
				echo " N/A ";
			}
		?>
			</span></td><td style="width: 300px">
				<span> Show Classification: <?php
					echo $vars['genre'];
				?></span>
			</td><td style=" min-width:'225px'">
				<?php
				$getgen = "select * from `genre` where genreid='" . $vars['genre'] . "' ";
				$reqar = mysqli_query($con, $getgen);
				$req = mysqli_fetch_array($reqar);
				if($req['CCType']=='0'){
                    $SQL_CC_COUNT = "SELECT 
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%' and cancon='1') AS CC_Num,
                    (SELECT round(((CC_Num / Total)*100),2)) AS Percent";
                    if(!$CC_PER_RES = mysqli_query($con, $SQL_CC_COUNT)){
                        echo "<span class='ui-state-highlight ui-corner-all'>".mysql_error()."</span>";
                        //break;
                    }
                    else{
                        $PER_CC = mysqli_fetch_array($CC_PER_RES);
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
				    $resultCC = mysqli_query($con, $SQLCOUNTCC);
                    $CC_VARS = mysqli_fetch_array($resultCC);
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
			</td><td style="width: 225px">
				<?php
                if($req['PlType']=='0'){
                    $SQL_PL_COUNT = "SELECT 
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
                    (SELECT count(*) FROM song WHERE callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and category not like '1%' and category not like '4%' and category not like '5%' and Playlistnumber IS NOT NULL) AS Count,
                    (SELECT round(((Count / Total)*100),2)) AS Percent";
                    if(!$PL_PER_RES = mysqli_query($con, $SQL_PL_COUNT)){
                        echo "<span class='ui-state-highlight ui-corner-all'>".mysql_error()."</span>";
                        //break;
                    }
                    else{
                        $PER_PL = mysqli_fetch_array($PL_PER_RES);
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

				    $SQLCOUNTPL = "Select count(songid) AS EnteredPL from `song` where callsign='" . $CALL . "' and programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and playlistnumber is not null ";
				    $resultPL = mysqli_query($con, $SQLCOUNTPL);
                    $PL_VARS = mysqli_fetch_array($resultPL);
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
					$SHOWSQL = "SELECT * FROM `program` WHERE active='1' and callsign='".$CALL."' ";
					$SHOWARRAY = mysqli_query($con, $SHOWSQL);
					while($OPSH = mysqli_fetch_array($SHOWARRAY)){
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
    <!--<div id="hdw_prompt" style="margin: 0 auto 0 auto; width: 1354px; background-color: #000; color: white; display:none;"><button onclick="ShowHardware()" title="Show"><span class="ui-icon ui-icon-carat-1-s"></span></button><span>Hardware Control</span></div>-->
    <div id="hdw" style="margin: 0 auto 0 auto; width: 1354px; background-color: #000; color: white; text-align: left;">
        <?php
            if(False){
                echo "<span id=\"HDW_title_open\"><!--<button onclick=\"HideHardware()\" title=\"Hide\"><span class=\"ui-icon ui-icon-carat-1-n\"></span></button>--><span>Hardware Control</span></span>";
                if($_SESSION['access']==2){
                    $Hardware_Query="SELECT hardware.*, device_codes.Manufacturer FROM hardware INNER JOIN device_codes ON hardware.device_code=device_codes.Device WHERE station ='$CALL' and in_service='1' and ipv4_address IS NOT NULL group by hardware.hardwareid order by friendly_name ASC";
                }
                else{
                $Hardware_Query="SELECT hardware.*, device_codes.Manufacturer FROM hardware INNER JOIN device_codes ON hardware.device_code=device_codes.Device WHERE station ='$CALL' and in_service='1' and ipv4_address IS NOT NULL and hardware.room=(SELECT `hardware`.`room` AS `room_ip` FROM hardware WHERE hardware.ipv4_address='".$_SERVER['REMOTE_ADDR']."' and `hardware`.`hardware_type`='1' and `hardware`.`in_service`='1' order by `hardware`.`hardwareid` LIMIT 1) group by hardware.hardwareid order by friendly_name ASC";
                }
                if(!$Equipment_List = mysqli_query($con, $Hardware_Query)){
                    error_log("Encountered Error: p3update.php, Query HArdware_Query returned invalid result: ".mysql_error());
                }
                $BOOTH = 0;
                while($Equipment_row = mysqli_fetch_array($Equipment_List)){
                    if($Equipment_row['ipv4_address']==$_SERVER['REMOTE_ADDR'] || $_SESSION['access']==2){

                        echo "<hr><div id=\"toolbar".$Equipment_row['hardwareid']."\"  style=\"color: white; background:#000; width:100%; display: block\">
                        <span >".strtoupper($Equipment_row['Manufacturer'])." ".$Equipment_row['device_code']." - ".$Equipment_row['friendly_name']."</span><span style='width:100%'>&nbsp</span>
                        <span id='RES".$Equipment_row['hardwareid']."' style=\"color: white; background: #7690a3; width:100%; text-align: center; background-color: #7690a3;\">&nbsp;- DENON - </span><br>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','8','".$Equipment_row['hardwareid']."')\" title=\"Eject\"><span class=\"ui-icon ui-icon-eject \"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','20','".$Equipment_row['hardwareid']."')\" title=\"CUE NEXT\"><span class=\"ui-icon ui-icon-arrowthickstop-1-s\"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','1','".$Equipment_row['hardwareid']."')\" title=\"Play\"><span class=\"ui-icon ui-icon-play\"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','9','".$Equipment_row['hardwareid']."')\" title=\"Pause\"><span class=\"ui-icon ui-icon-pause\"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','2','".$Equipment_row['hardwareid']."')\" title=\"Stop\"><span class=\"ui-icon ui-icon-stop\"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','5','".$Equipment_row['hardwareid']."')\" title=\"Previous\"><span class=\"ui-icon ui-icon-seek-first\"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','4','".$Equipment_row['hardwareid']."')\" title=\"Next\"><span class=\"ui-icon ui-icon-seek-end\"></span></button>
                        <button class=\"HID-RE-".$Equipment_row['hardwareid']."\" onclick=\"Update_Device_Status('RES".$Equipment_row['hardwareid']."','".$Equipment_row['hardwareid']."')\" title=\"Refresh Device\"><span class=\"ui-icon ui-icon-refresh\"></span></button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" style=\"float: right\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','7','".$Equipment_row['hardwareid']."')\">Wake (Soft On)</button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" style=\"float: right\" onclick=\"Query_Device('RES".$Equipment_row['hardwareid']."','6','".$Equipment_row['hardwareid']."')\">Standby (Off)</button>
                        <button class=\"HID-".$Equipment_row['hardwareid']."\" style=\"float: right\" onclick=\"Get_Info('title001','artin','albin','".$Equipment_row['hardwareid']."')\">Get Information</button>
                        </div>";
                    }
                }
            }

        ?>
    </div>
	<div id="content" style="width: <?php echo $SETW ?>">
        <!-- Emergency Alert System (EAS)-->
        <div id="EAS"></div>
		<table border="0" class="tablecss">
			<thead><tr>
				<th style="width: 50px">
					<span title="Changed Value" class="ui-icon ui-icon-transferthick-e-w"></span>
				</th>
				<th style="width: 200px">
					Type
				</th>
				<th style="width: 75px">
					Playlist
				</th>
				<th style="width: 75px">
					Spoken
				</th>
				<th style="width: 75px">
					Time
				</th>
                <?php
				    if($vars['Display_Order']==0){
                        echo "<th width=\"150px\">Title</th><th width=\"150px\">Artist</th><th width=\"150px\">Album</th>";
                    }
                    elseif($vars['Display_Order']==1){
                        echo "<th width=\"150px\">Artist</th><th width=\"150px\">Album</th><th width=\"150px\">Title</th>";
                    }
                    else{
                        echo "<th width=\"150px\">Title</th><th width=\"150px\">Artist</th><th width=\"150px\">Album</th>";
                    }
                    ?>
				<th style="width: 150px">
					Composer
				</th>
				<th style="width: 25px">CC</th>
				<th style="width: 25px">Hit</th>
				<th style="width: 25px">Ins</th>
				<th style="width: 25px">Fin</th>
				<th style="width: 25px">Type</th>
				<th style="width: 75px">Language</th>
				<th><span title="Note" class="ui-icon ui-icon-tag"></span></th>
				<th><span title="Delete Record" class="ui-icon ui-icon-trash"></span></th>
			</tr></thead><tbody>


<?php
	$FETSON = "SELECT * from `song` where programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL ."' order by time ".$vars['displayorder'];
	//echo $FETSON; // sql qUEREY pRINT oUT

	//echo $FETSON; //DEBUG USE ONLY
	if(!$SONRES = mysqli_query($con, $FETSON))
	{
		echo "FETCH ERROR: Could not Fetch Songs performed, Server Returned (".mysql_errno().": ".mysql_error().") <br/><br/>SQL:";
		echo $FETSON;
	}
	else{
		$CONT = 0;
		while($SONGS = mysqli_fetch_array($SONRES)){
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
			// INPUT PARAMS
            if($vars['Display_Order']==0){
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
            }
            elseif($vars['Display_Order']==1){
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
			    echo "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\" required type=\"text\" name=\"titles[]\" value=\"" . $SONGS['title'] . "\" maxlength=\"90\" /> </td>";
            }
            else{
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
            }
			//  COMP+
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
			echo "/></td>";
			echo "<td><input disabled onChange=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"complete[]\" value='".$SONGS['songid']."' ";
			if(isset($SONGS['complete'])){
                            if( $SONGS['complete'] == "1"){
                                    echo " checked ";
                            }
                        }
			echo "/></td> ";
			echo "<td>"; // Put Type Here
	        echo "<select name=type[] onChange=\"SetEdit('EDI".$CONT."')\">";
            echo "\n<option ";
            if(isset($SONGS['type'])){
                if($SONGS['type']=="BACKGROUND"){
                    echo " selected ";
                }

                echo " value='BACKGROUND'>BG</option>";
                echo "\n<option ";
                if($SONGS['type']=="NA" || !isset($SONGS['type'])){
                    echo " selected ";
                }
                echo "value='NA'>SA</option>";
                echo "\n<option ";
                if($SONGS['type']=="THEME"){
                    echo " selected ";
                }
                echo "value='THEME'>TH</option>";
                echo "\n</select>";
            }
            else{
                echo " selected value='NA'>--</option></select> ";
            }
			echo "</td>";
			$LANS = mysqli_fetch_array(mysqli_query($con, "SELECT languageid from `language` where songid=\"" . $SONGS['songid'] . "\" "));
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
	            		<input type="text" hidden name="program" value=<?php echo "\"" . urlencode(stripslashes($SHOW)) . "\"" ?> />
	            		<input type="text" hidden name="user_date" value=<?php echo "\"" . $DATE . "\"" ?> />
	            		<input type="text" hidden name="user_time" value=<?php echo "\"" . $START . "\"" ?> />
					<input type="submit" value="Submit Changes"></form></td><td>
					<form action="../p2insertEP.php" method="POST">
						<input type="text" hidden name="callsign" value=<?php echo "\"" . $CALL . "\"" ?> />
	            		<input type="text" hidden name="program" value=<?php echo "\"" . urlencode(stripslashes($SHOW)) . "\"" ?> />
	            		<input type="text" hidden name="user_date" value=<?php echo "\"" . $DATE . "\"" ?> />
	            		<input type="text" hidden name="user_time" value=<?php echo "\"" . $START . "\"" ?> />
	            		<input type="submit" value="Return to Addition"/>
	            	</form></td><td>
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
						<input type="text" hidden name="callsign" value=<?php echo "\"" . $CALL . "\"" ?> />
	            		<input type="text" hidden name="program" value=<?php echo "\"" . urlencode(stripslashes($SHOW)) . "\"" ?> />
	            		<input type="text" hidden name="user_date" value=<?php echo "\"" . $DATE . "\"" ?> />
	            		<input type="text" hidden name="user_time" value=<?php echo "\"" . $START . "\"" ?> />
						<input type="submit" value="Reset" />
					</form></td><td>
				<!--<form method="POST" action="/masterpage.php"><input type="submit" value="Menu"/></form>-->
				</td>
				<td style="width: 100%" align="right"><img src="../../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
    <script type="text/javascript">
        <?php
        print "station = \"$CALL\";\n";
        ?>
     function SetRem(chk, ID, ROW, COUNT) {
         if (chk == true) {
             document.getElementById(ID).style.background = 'red';
         }
         else {
             //alert('UNCHECK')
             if (COUNT % 2) {
                 document.getElementById(ID).style.background = '#DAFFFF'; //'#F9F9AA';
             }
             else {
                 document.getElementById(ID).style.background = 'white';
             }
         }
     }

     function SetEdit(Row) {
         //alert(Row);
         //document.forms['general'].Row.checked="true";
         document.getElementById(Row).checked = "true";
     }
     function HideHardware() {
         $("#HDW_title_open").hide();
         $("#hdw_prompt").show();
         $("#hdw").hide();
     }

     function ShowHardware() {
         $("#hdw").show();
         $("#HDW_title_open").show();
         $("#hdw_prompt").hide();
     }

     function SetNote(ELID, EDI) {
         //var VAL = document.getElementById(ELID).value;
         //alert(document.getElementById(ELID).value)
         document.getElementById(EDI).checked = "true";
         var NOTE = prompt("Notes for individual song (90 char Max)", document.getElementById(ELID).value);
         if (NOTE != null) {
             document.getElementById(ELID).value = NOTE;
         }
     }
     $(document).ready(function () {
         // Load Emergency Information
         GetEAS('EAS', '../../../', station);
         setInterval(function () {
            GetEAS('EAS', '../../../', station);
         }, 15000);
     });
	</script>
</body>
</html>
