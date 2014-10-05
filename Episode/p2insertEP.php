<?php
    include_once "../TPSBIN/functions.php";
      sec_session_start();
      $ADIDS = array();
      $ADOPT = "";
      $END_TIME_VAL="00:00:00";
      $DEBUG = FALSE;
      $FINALIZED = !isset($EPINFO['endtime']);
      if(isset($_POST['Description'])){
        $DESCRIPTION = addslashes($_POST['Description']);
      }
      else{
          $DESCRIPTION = "";
      }

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../logout.php');}
}
else{
	echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
}
	$error = array();
	$warning = array();
	
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
	if($RadioDJ == "1"){
		array_push($warning,"<strong><br/>Warning: At " . substr($switchArray['timestamp'],-8,5) . " the 24 Hour system was live to air<br/><br/></strong>");
	}
    elseif ($booth2 == "1"){
        array_push($warning,"<strong><br/>Notice: Booth 2 is on air<br/><br/></strong>");
    }
    elseif($booth1 == "0"){
        //array_push($warning,"<strong><br/>Notice: No valid audio source is to air. pleae check switch or warning settings<br/><br/></strong>");
    }
	// END Switch Check
	
        $SHOWQ = "select callsign from program where programname='" . addslashes($_POST['program']) . "' ";
        $SHOWQU = mysql_query($SHOWQ,$con);
        $CALLROWS = mysql_fetch_array($SHOWQU);
        $CALLSHOW = $CALLROWS["callsign"];

        $INSEPSEL = "select * from episode LEFT JOIN program on program.programname=episode.programname where episode.callsign='" . addslashes($CALLSHOW) . "' and episode.programname='" . addslashes($_POST['program']) . "' and episode.date='" . addslashes($_POST['user_date']) . "' and episode.starttime='" . addslashes($_POST['user_time']) . "' order by episode.date";
        $RESEPSEL=mysql_query($INSEPSEL,$con);
        $EPINFO=mysql_fetch_array($RESEPSEL);
        $ADINS=FALSE;
        
		$SETTINGS = mysql_fetch_array(mysql_query("SELECT * FROM station WHERE callsign='".$CALLSHOW."' "));
		
		
		//echo $_POST['title'];
        if(mysql_numrows($RESEPSEL)=="0"){
        	if($_POST['brType']>0){
        		$inep = "insert into episode (callsign, programname, date, starttime, prerecorddate, description, IP_Created) values ( '" . addslashes($CALLSHOW) . "', '" . addslashes($_POST['program']) . "', '" . addslashes($_POST['user_date']) . "', '" . addslashes($_POST['user_time']) . "', '" . addslashes($_POST['prdate']) . "', '$DESCRIPTION','".$_SERVER['REMOTE_ADDR']."' )";
        	}
		  else if(!isset($_POST['enprerec']))
          {
            $inep = "insert into episode (callsign, programname, date, starttime, description, IP_Created) values ( '" . addslashes($CALLSHOW) . "', '" . addslashes($_POST['program']) . "', '" . addslashes($_POST['user_date']) . "', '" . addslashes($_POST['user_time']) . "', '$DESCRIPTION','".$_SERVER['REMOTE_ADDR']."' )";
            
          }
          else
          {
            $inep = "insert into episode (callsign, programname, date, starttime, prerecorddate, description, IP_Created) values ( '" . addslashes($CALLSHOW) . "', '" . addslashes($_POST['program']) . "', '" . addslashes($_POST['user_date']) . "', '" . addslashes($_POST['user_time']) . "', '" . addslashes($_POST['prdate']) . "', '$DESCRIPTION','".$_SERVER['REMOTE_ADDR']."' )";
          }
            if(!mysql_query($inep,$con))
            {
              echo 'SQL Error<br />';
              echo mysql_error() . "<br/>";
			  die($inep . "<br/>");
            }
		$RESEPSEL=mysql_query($INSEPSEL,$con);
		$EPINFO=mysql_fetch_array($RESEPSEL);
		  
        }
        else if(mysql_numrows($RESEPSEL)>"1"){
          echo 'warning, multiple episodes with same information!';
        }
        $program = "select * from performs order by programname";
        $prog=mysql_query($program,$con);
        
        $options="";
        while ($row=mysql_fetch_array($prog)) {
            $name=$row["programname"];
            $options.="<OPTION VALUE=\"".$name."\">".$name."</option>";
        }
        if(!isset($_POST['title'])){
          //echo 'no title';
        }
        else{
          if($_POST['title']!=""){
			//echo "<p>VER NOT EMPTY</p>";
              //dynamic SQL CREATION
              
              $indyns = "insert into SONG (callsign, programname, date, starttime";
              $BUFFS = "'" . addslashes($CALLSHOW) . "' , '" . addslashes($_POST['program']) . "' , '" . addslashes($_POST['user_date']) . "' , '" . addslashes($_POST['user_time']) . "'";
              if (isset($_POST['instrumental'])){
                $indyns.=", instrumental";
                $BUFFS.=", '1' ";
              }
              if ($_POST['time']!=""){
                $indyns.=", time";
                $BUFFS.=", '" . addslashes($_POST['time']) . "' ";
              }
			  
              if (isset($_POST['title'])){
                	if($_POST['cat']=="51"){
                		$QRR = mysql_fetch_array(mysql_query("select AdName, Language from adverts where AdId='".addslashes($_POST['title'])."' "));
						$BUFFS.=", '" . $QRR['AdName'] . "' ";
                	}
					else{
						$BUFFS.=", '" . addslashes($_POST['title']) . "' ";
					}
                $indyns.=", title";
              }
              if (isset($_POST['album'])){
                $indyns.=", album";
                $BUFFS.=", '" . addslashes($_POST['album']) . "' ";
              }
              if (isset($_POST['composer'])){
                $indyns.=", composer";
                $BUFFS.=", '" . addslashes($_POST['composer']) . "' ";
              }
              if (isset($_POST['note'])&&$_POST['note']!=""){
                $indyns.=", note";
                $BUFFS.=", '" . addslashes($_POST['note']) . "' ";
              }
			  if ($_POST['spokenmin']!=""){
                $indyns.=", Spoken";
                $BUFFS.=", '" . addslashes($_POST['spokenmin']) . "' ";
              }
              if (isset($_POST['artist'])){
                $indyns.=", artist";
                $BUFFS.=", '" . addslashes($_POST['artist']) . "' ";
              }
              if (isset($_POST['cancon'])){
                $indyns.=", cancon";
                $BUFFS.=", '1' ";
              }
              if ($_POST['playlist']!=""){
                $indyns.=", playlistnumber";
                $BUFFS.=", '" . addslashes($_POST['playlist']) . "' ";
              }
              if (isset($_POST['cat'])){
              	if($_POST['cat']=='51'){
              		if(isset($_POST['AdNum'])){
              								
							// UPDATE Playcount
							$SPupSQL = "select SponsId from program where programname='" . addslashes($_POST['program']) . "' and callsign='" . addslashes($CALLSHOW) . "' and SponsId is not null";
							if(!$SPup = mysql_query($SPupSQL)){
								array_push($error, mysql_errno() . "</td><td>" . mysql_error()); 
							}
							//echo mysql_num_rows($SPup);
							if(mysql_num_rows($SPup)==0){
								$playcountsql = "SELECT Playcount+1 as result from adverts where AdId='".addslashes($_POST['AdNum'])."'";
								if(!$playcount_arr = mysql_query($playcountsql)){
									echo mysql_error();
								}
								$playcount = mysql_fetch_array($playcount_arr);
								//echo $playcount['result'];
								$UPAD = "UPDATE adverts SET Playcount='".$playcount['result']."' where AdId='".addslashes($_POST['AdNum'])."' or XREF='".addslashes($_POST['AdNum'])."'";
		              			/*$UPAD = "update adverts set Playcount=Playcount+1 where AdId=\"" . $_POST['AdNum'] . "\" ";
								$ADQN = mysql_query("select XREF from adverts where AdId='" . $_POST['AdNum'] . "' and XREF IS NOT NULL");
								if(mysql_num_rows($ADQN)!=0){
									$XREF=mysql_fetch_array($ADQN);
									$UPXREF = "update adverts set Playcount=(select Playcount as result where AdId=\"" . $_POST['AdNum'] . "\") where AdId=\"" . $XREF['XREF'] . "\" ";
								}*/
								/*else{
								 	//Not Required to report as many ads do not have XREF
									//array_push($error, mysql_errno() . "</td><td>" . mysql_error());
									array_push($error,"999</td><td> XREF not Defined (ignore for now)"); 
								}*/
									// SET FLAG IF NOT AVAILABLE
		              			$result_Flag = mysql_query("select Playcount from adverts where AdId='" . addslashes($_POST['AdNum']) . "' and Category='51'");
                                //$result_AdType = mysql_query("select Playcount from adverts where AdId='" . addslashes($_POST['AdNum']) . "' and Category='51'");
		              			$FlCheck = mysql_fetch_array($result_Flag);
								//echo $FlCheck['Playcount'];
								$Sel51Flag = $minplaysql51 = "select MIN(Playcount) from adverts where Category='51' and Active='1' and Friend='1' ";
								$Min51Flag = mysql_query($Sel51Flag);
								$flagLevel = mysql_fetch_array($Min51Flag);
								//echo $flagLevel['MIN(Playcount)'];
								if($FlCheck['Playcount']>$flagLevel['MIN(Playcount)']){
									$indyns.=", AdViolationFlag";
		                			$BUFFS.=", '1' ";
								}
								
								if(!mysql_query($UPAD)){
									echo "<div class='error'>AD ERROR: ".mysql_error() . " <br/>Using: $UPAD</div>";
								}
								else{
                                    $ADINS=TRUE;
									/*if($UPXREF!=""){
										if(!mysql_query($UPXREF)){
											echo $UPXREF;
											echo "XREF ERROR:" . mysql_error();
										}
									}*/
								}
							}
							
					}
					/*else {
						$UPAD = "update adverts set Playcount=Playcount+1 where AdName LIKE \"%" . $_POST['title'] . "%\" and Category!='51'";
					}*/
					
					//echo $UPAD;
					
              	}
                else{
                    $ADINS=FALSE;
                }
                $indyns.=", category";
                $BUFFS.=", '" . addslashes($_POST['cat']) . "' ";
              }
              if (isset($_POST['hit'])){
                $indyns.=", hit";
                $BUFFS.=", '1' ";
              }
              $BUFFS.=" )";
              $indyns.=") values ( ";
              $DYNAMIC = $indyns . $BUFFS;
			  //echo $DYNAMIC;
              if(!mysql_query($DYNAMIC,$con))
              {
                echo 'SQL Error<br />';
                echo mysql_error();
              }
              else //This is executed if the song is inserted
              {
              			$LASTLINK =  mysql_insert_id($con);
			  			if(!isset($QRR['Language'])){
			  				$LANGIN = addslashes($_POST['lang']);
						}
						else{
							$LANGIN = $QRR['Language'];
						}
                          $langDef = "insert into language values ('" . addslashes($CALLSHOW) . "', '". addslashes($_POST['program']) ."', '" . addslashes($_POST['user_date']) . "', '". addslashes($_POST['user_time']) . "', '" . addslashes($LASTLINK) . "', '" . $LANGIN . "')";
                          if(!mysql_query($langDef,$con))
                          {
                              echo 'SQL Error, Language Insertion<br />';
                              echo mysql_error();
                          }
                          if($ADINS){
                            $TRAFFIC_SQL="insert into trafficaudit (`songid`,`advertid`) values ('".addslashes($LASTLINK)."','".addslashes($_POST['AdNum'])."')";
                            if(!mysql_query($TRAFFIC_SQL,$con)){
                              echo 'SQL Error, traffic error - Generation<br />';
                              echo mysql_error();
                            }
                          }
              }
            }
        }

        /*////////////////////////////////////////////
        //              GENRE SELECTION             //
        //                                          //
        ////////////////////////////////////////////*/
        
        // This information is needed by either method 
        // Using updated code
        
		$SQLProg = "SELECT Genre.*, Program.length from Genre, Program where Program.programname=\"" . addslashes($_POST['program']) . "\" and program.callsign=\"" . addslashes($CALLSHOW) . "\" and Program.genre=Genre.genreid";
		if(!($result = mysql_query($SQLProg))){
			echo "Program Error 001 " . mysql_error();
		}
		if(!($Requirements = mysql_fetch_array($result))){
			echo "Program Error 002 " . mysql_error();
		}
		$SQL2PR = "SELECT * from Program where programname=\"" . addslashes($_POST['program']) . "\" and callsign=\"" . addslashes($CALLSHOW) . "\" ";
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
		$SQLCOUNTCC = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and cancon='1' ";
		$resultCC = mysql_query($SQLCOUNTCC);
		$RECCC = mysql_num_rows($resultCC);
		
		// COUNT PLAYLIST
		$SQLCOUNTPL = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and playlistnumber IS NOT NULL ";
		if($SETTINGS['ST_PLLG']=='1'){
			$SQLCOUNTPL .="group by playlistnumber";	
		}
		$resultPL = mysql_query($SQLCOUNTPL);
		$RECPL = mysql_num_rows($resultPL);
		
		//COUNT ADS
		$SQLCOUNT51 = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and category='51' and AdViolationFlag is null";
		$result51 = mysql_query($SQLCOUNT51);
		$REC51 = mysql_num_rows($result51);
		
		//COUNT PSA
		$SQLCOUNTPROMO = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and category='45'";
		$SQLCOUNTPSA = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and category like '1%' and (title like '%PSA%' or Artist like 'Station PSA')";
		//$SQLCOUNTPSA = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and category like '1%' and title like '%Promo%' ";
		$resultPSA = mysql_query($SQLCOUNTPSA);
		$resultPROMO = mysql_query($SQLCOUNTPROMO);
		$RECPSA = mysql_num_rows($resultPROMO);
		$RECPSA += mysql_num_rows($resultPSA);
        

        /*[TODO] update function to include system check (JSON)*/
        $Foobar_Enabled_Query = "SELECT hardwareid from hardware where `device_code`='Foobar2000' and `ipv4_address`='".$_SERVER['REMOTE_ADDR']."' and `in_service`='1';";
        $Foobar_array = mysql_query($Foobar_Enabled_Query);
        if(mysql_num_rows($Foobar_array)>0){
            $Foobar_Enabled=TRUE;
        }
        else{
            $Foobar_Enabled=FALSE;
        }
        $RDS_Enabled=TRUE;
        $Switch_Enabled=TRUE;

?>
<!DOCTYPE HTML>
<html>
<head>
	<!--<script src="../js/jquery/js/jquery-1.9.1.min.js"></script>-->
    <script type="text/javascript" src="../TPSBIN/JS/Episode/V2CoreJS.js"></script>
    <script type="text/javascript" src="../TPSBIN/JS/Control/Device.js"></script>
    <script type="text/javascript" src="../js/jquery/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/jquery/js/jquery-ui-1.11.0/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../TPSBIN/JS/GLOBAL/Utilities.js"></script>
    <script type="text/javascript" src="../TPSBIN/JS/Episode/CoreV2.js"></script>
    <link rel="stylesheet" href="../js/jquery/css/ui-lightness/jquery-ui-1.10.0.custom.min.css"/>
    <style>
        .ui-autocomplete-loading {
            background: white url('../images/GIF/ajax-loader3.gif') right center no-repeat;
          }
    </style>
	<!--<script src="../js/jquery/js/jquery-ui-1.10.0.custom.min.js"></script>-->
    <!--<script>
        <?php          
            if(!isset($_SESSION['hardware_prompt'])){
                $_SESSION['hardware_prompt']="TRUE";
            }
            if($_SESSION['hardware_prompt']=="FALSE"){
                echo "global var hardware=off;";
            }
            else{
                echo "global var hardware=on;";
            }
         ?>
    </script>-->
	<script src="../js/jquery-blockui.js"></script>
	<script type="text/javascript">
     $(document).ready(function () {
         $('#artin').autocomplete({
             source: "../MusicLib/DB_Search_Artist.php",
             minLength: 2
         });
        <?php          
            if($_SESSION['hardware_prompt']=="FALSE"){
                echo "HideHardware();";
            }
         ?>
         // set finalize actions and update field
         $("#end_time").change(UpdateFinalize);

         // set displays
        <?php
            //workers
            if($RDS_Enabled){ print("Display_RDS();"); };
            if($Switch_Enabled){ print("Display_Switch();"); };
            if($Foobar_Enabled){ print("Foobar2000();"); };
         ?>
         // Load Emergency Information
         GetEAS('EAS', '../');
         setInterval(function () {
             GetEAS('EAS', '../');
         }, 15000);

         setInterval(function () {
             Display_RDS();
             Display_Switch();
         }, 20000);
         /*$('input[name=sub]').click(function() { 
         $.blockUI({ message: '<h1><img src="/images/GIF/ajax-loader1.gif" />Processing...</h1>' }); 
         //test(); 
         //$.blockUI({ message: '<h1><image src="/images/GIF/ajax-loader1.gif"/>Processing...</h1>' }); 
         setTimeout(function() { 
         $.unblockUI({ 
         onUnblock: function(){ alert('The server was unable to process your request in a reasonable time. \nPlease resubmit your data'); } 
         }); 
         }, 4000);
         }); */
         $('form').submit(function () {
             $.blockUI({ message: "<h1 style='width:width: max-content; text-align: center;' >Processing...</h1><progress id='pb_form_submit'></progress>" });
             //test(); 
             //$.blockUI({ message: '<h1><image src="/images/GIF/ajax-loader1.gif"/>Processing...</h1>' }); 
             setTimeout(function () {
                 $.unblockUI({
                     onUnblock: function () {
                         /*alert('The server was unable to process your request in a reasonable time.');*/
                         $("#Alert").html("Something didn't go as planned... the server was being moody, or you pressed cancel...");
                         $("#Alert").slideDown("slow", function () {
                             $("#Alert").slideUp(600);
                         }).delay(3000);
                     }
                 });
             }, 4000);
         });
     }); 

</script> 
    <script>
        $(function () {
            var name = $("#name"),
            time = $("#time_final_confirm"),
            password = $("#password"),
            allFields = $([]).add(name).add(time).add(password),
            tips = $(".validateTips");

            function updateTips(t) {
                tips
              .text(t)
              .addClass("ui-state-highlight");
                setTimeout(function () {
                    tips.removeClass("ui-state-highlight", 1500);
                }, 500);
            }

            function checkLength(o, n, min, max) {
                if (o.val().length > max || o.val().length < min) {
                    o.addClass("ui-state-error");
                    updateTips("Length of " + n + " must be between " +
                min + " and " + max + ".");
                    return false;
                } else {
                    return true;
                }
            }

            function checkRegexp(o, regexp, n) {
                if (!(regexp.test(o.val()))) {
                    o.addClass("ui-state-error");
                    updateTips(n);
                    return false;
                } else {
                    return true;
                }
            }

            $("#dialog-form").dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true,
                open: function (){
                    $("#time_final_confirm").val($("#end_time").val());
                },
                buttons: {
                    "Confirm": function () {
                        var bValid = true;
                        allFields.removeClass("ui-state-error");

                        //bValid = bValid && (time.val() != "");
                        bValid = bValid && checkLength($("#time_final_confirm"), "time", 3, 9);

                        if (bValid) {
                            //alert($("#time_final_confirm").val());
                            $("#end_time").val($("#time_final_confirm").val());
                            $(this).dialog("close");
                            $("#Complete").submit();
                        }
                    },
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                },
                close: function () {
                    allFields.val("").removeClass("ui-state-error");
                }
            });

            $("#confirm_final")
            .button()
            .click(function () {
                $("#dialog-form").dialog("open");
            });
        });
  </script>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>Log Addition</title>
</head>
<body onload="load()" 
<?php 
if(false){
	echo "onunload=\"return confirm('WARNING: Unfinalized Episode\\n\\nThis episode is not finalized. Are you sure you want to exit?')\" ";
}
?>
>
	 
    <!--<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>-->

        <table border="0" style="text-align:center; width:1350px;">
        <tr><td colspan="6">
           <img src="../<?php echo $_SESSION['logo']; ?>" alt="ckxu" height="90px"/>
        </td>
        <td style="width: 250px; height: 110px;" id="switchstat" colspan="1">
        	<!--<iframe src="EPV3/Switch.php" height="100px" width="100%" seamless="seamless" style="border:none">Iframe Not Supported</iframe>-->
            <span id="switch_status"></span>
        </td></tr>
        <tr><td width="800px" colspan="1" style="background-color:white;">
	<h2>Program Log Addition</h2></td>
	<?php
	echo "</td><td width=\"500px\"  style=\"background-color:white;\">";
	echo "Show Classification:  <strong>" . $CLA . "</strong>";
	echo "</td><td width=\"500px\" ";
		
		if(isset($Req2['SponsId'])){
			echo "style=\"background-color:".$SETTINGS['ST_ColorNote'].";\" /><span>Sponsor : ";
			$SPONS_SQL = " select * from adverts where AdId='".$Req2['SponsId']."' ";
			$SPONS = mysql_fetch_array(mysql_query($SPONS_SQL));
			echo $SPONS['AdName'];
		}
		else{
			echo " style=\"background-color:white\" /><span>Sponsor :  None ";
		}
		"</span>";
		
	// #################### ADS  ##################
	$ADS = ceil(($Requirements['length']*$SETTINGS['ST_ADSH'])/60);
	echo "</td><td width=\"250px\" style=\"background-color:";
	if($REC51<$ADS){
		echo $SETTINGS['ST_ColorFail'];
	}
	else{
		echo $SETTINGS['ST_ColorPass'];
	}
	echo ";\" >";
		echo "<span>ADs: <strong>".$REC51."/".$ADS."</strong></span>";
		
	// #################### PSA ##############################
	$PSA = ceil(($Requirements['length']*$SETTINGS['ST_PSAH'])/60);
	echo "</td><td width=\"250px\" style=\"background-color:";
	if($RECPSA<$PSA){
		echo $SETTINGS['ST_ColorFail'];
	}
	else{
		echo $SETTINGS['ST_ColorPass'];
	}
	echo ";\" >";
		echo "<span>PSA/Promo: <strong>".$RECPSA."/".$PSA."</strong></span>";
	echo "</td><td width=\"500px\""; 
	
	// ################ REQ CC PL ##################
    if($Requirements['CCType']=='0'){
        $SQL_CC_COUNT = "SELECT 
        (SELECT count(*) FROM song WHERE callsign='" . addslashes($EPINFO['callsign']) . "' and programname='" . addslashes($EPINFO['programname']) . "' and date='" . addslashes($EPINFO['date']) . "' and starttime='" . addslashes($EPINFO['starttime']). "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
        (SELECT count(*) FROM song WHERE callsign='" . addslashes($EPINFO['callsign']) . "' and programname='" . addslashes($EPINFO['programname']) . "' and date='" . addslashes($EPINFO['date']) . "' and starttime='" . addslashes($EPINFO['starttime']). "' and category not like '1%' and category not like '4%' and category not like '5%' and cancon='1') AS CC_Num,
        (SELECT round(((CC_Num / Total)*100),2)) AS Percent";
        if(!$CC_PER_RES = mysql_query($SQL_CC_COUNT)){
            echo "<span class='ui-state-highlight ui-corner-all'>".mysql_error()."</span>";
            //break;
        }
        else{
            $PER_CC = mysql_fetch_array($CC_PER_RES);
            echo "<span ";
            if(floatval($PER_CC['Percent']) < floatval($Requirements['canconperc'])*100){
                echo "style=\"background-color:".$SETTINGS['ST_ColorFail'].";text-align:center;\" >";
            }
            else{
                echo "style=\"background-color:".$SETTINGS['ST_ColorPass'].";text-align:center;\" >";
            }
            echo "Canadian Content Required</span><br/><span>";
			echo $PER_CC['Percent']." /".(floatval($Requirements['canconperc'])*100)."%";
            if($DEBUG){
                echo "[".$PER_CC['CC_Num']."/".$PER_CC['Total']."]";
            }
        }
    }
    else{
        if($RECCC>=$CC){
	 	    echo "style=\"background-color:".$SETTINGS['ST_ColorPass'].";\">";
	    }
	    else{
		    echo "style=\"background-color:".$SETTINGS['ST_ColorFail'].";\">";
	    }
		    echo "Canadian Content Required:  <strong>" . $RECCC . "/" . $CC . "</strong>";
    }
    echo "</td><td width=\"300px\"";
    //---------------------------------//
    //-----------< PLAYLIST >----------//
    //---------------------------------//
	if($Requirements['PlType']=='0'){
        // WORKING WITH PERCENTAGE
        // GET PERCENTAGE FROM DB
        
        $SQL_PL_COUNT = "SELECT 
        (SELECT count(*) FROM song WHERE callsign='" . addslashes($EPINFO['callsign']) . "' and programname='" . addslashes($EPINFO['programname']) . "' and date='" . addslashes($EPINFO['date']) . "' and starttime='" . addslashes($EPINFO['starttime']). "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
        (SELECT count(*) FROM song WHERE callsign='" . addslashes($EPINFO['callsign']) . "' and programname='" . addslashes($EPINFO['programname']) . "' and date='" . addslashes($EPINFO['date']) . "' and starttime='" . addslashes($EPINFO['starttime']). "' and category not like '1%' and category not like '4%' and category not like '5%' and Playlistnumber IS NOT NULL) AS Count,
        (SELECT round(((Count / Total)*100),2)) AS Percent";
        if(!$PL_PER_RES = mysql_query($SQL_PL_COUNT)){
            echo "style=\"background-color:".$SETTINGS['ST_ColorFail'].";\" >";
            //echo "<span class='ui-state-highlight ui-corner-all'>".mysql_error()."</span>";
            //break;
        }
        else{
            $PER_PL = mysql_fetch_array($PL_PER_RES);
            echo "<span ";
            if(floatval($PER_PL['Percent']) < floatval($Requirements['playlistperc'])*100){
                echo "style=\"background-color:".$SETTINGS['ST_ColorFail']."; text-align:center;\" >";
            }
            else{
                echo "style=\"background-color:".$SETTINGS['ST_ColorPass']."; text-align:center;\" >";
            }
            echo "Playlist Required </span><br/><span>";
			echo $PER_PL['Percent']." /".(floatval($Requirements['playlistperc'])*100)."%";
            if($DEBUG){
                echo "[".$PER_PL['Count']."/".$PER_PL['Total']."]";
            }
        }
	}
    else{
	    // NUMERICAL REPRESENTATION
	    if($RECPL>=$PL){
	 	    echo "style=\"background-color:".$SETTINGS['ST_ColorPass'].";\">";
	    }
	    else{
		    echo "style=\"background-color:".$SETTINGS['ST_ColorFail'].";\">";
	    }
		echo "Playlist Required:  <strong>" . $RECPL. "/" . $PL . "</strong>";
    }
	echo "</td></tr>";
             /*
             $br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser they use.
            if(ereg("safari", $br)) {
              echo "<tr><td>
              <h3 width=\"100%\" style=\"background-color:yellow; color:black;\"><strong>WARNING: This browser does not support the needed HTML5 forms on Windows<br />
              please launch or download opera that supports these required forms. This does not apply to MAC OS</strong></h3>
              </td></tr>";
            }
            else {
              echo "<tr><td>
              <h3 width=\"100%\" style=\"background-color:red; color:white;\"><strong>WARNING: This browser does not support the needed HTML5 forms
              please launch or download opera that supports these required forms</strong></h3>
              </td></tr>";
            }
            */
			
			$SOCANC = "select Statement from socan where Enabled='1' and '" . addslashes($_POST['user_date']) ."' between start and end";
			$SOCANA = mysql_query($SOCANC);
			if(mysql_num_rows($SOCANA)>0){
                while($audit_entity=mysql_fetch_array($SOCANA)){
				    echo "<tr style=\"background-color:red; height:30px; color:white;\"><th style=\"text-align:center; font-size: 110%; font-familt:Verdana;\" colspan=\"100%\">";
				    echo $audit_entity['Statement'];
				    echo "</th></tr>";
                }
                //echo "<tr style=\"background-color:red; height:30px; color:white;\"><th colspan=\"100%\">";
			}
			if(sizeof($error) > 0){
				echo "<tr style=\"background-color:black; color:red;\"><th colspan=\"100%\">Errors</th></tr><tr>";
				$counter = 0;
				while($VAL = $error[$counter]){//array_pop($error)){
					echo "<tr style=\"background-color:white; color:red;\"><td>".$VAL."</td></tr>";
					$counter++;
				}
			}
			if(sizeof($warning) > 0){
                // style=\"background-color:Black; color:yellow;\"
				echo "<tr class='ui-state-error'><th colspan=\"100%\">Warnings &amp; Information</th></tr>";
				while($VAL = array_pop($warning)){
					echo "<tr class='ui-state-error'\"><td colspan=\"100%\"><span>".$VAL."<span></td></tr>";
				}
			}
        ?>
        </table>
    <div id="hdw_prompt" style="margin: 0 auto 0 auto; width: 1354px; background-color: #000; color: white; display:none;"><button onclick="ShowHardware()" title="Show"><span class="ui-icon ui-icon-carat-1-s"></span></button><span>Hardware Control</span></div>
    <div id="hdw" style="margin: 0 auto 0 auto; width: 1354px; background-color: #000; color: white">
        <?php
            if(FALSE){//implement system variable to determine if shown (stored in station)
                if($_SESSION['access']==2){
                    $Hardware_Query="SELECT hardware.*, device_codes.Manufacturer FROM hardware INNER JOIN device_codes ON hardware.device_code=device_codes.Device WHERE station ='".$EPINFO['callsign']."' and in_service='1' and ipv4_address IS NOT NULL group by hardware.hardwareid order by friendly_name ASC";
                }
                else{
                $Hardware_Query="SELECT hardware.*, device_codes.Manufacturer FROM hardware INNER JOIN device_codes ON hardware.device_code=device_codes.Device WHERE station ='".$EPINFO['callsign']."' and in_service='1' and ipv4_address IS NOT NULL and hardware.room=(SELECT `hardware`.`room` AS `room_ip` FROM hardware WHERE hardware.ipv4_address='".$_SERVER['REMOTE_ADDR']."' and `hardware`.`hardware_type`='1' and `hardware`.`in_service`='1' order by `hardware`.`hardwareid` LIMIT 1) group by hardware.hardwareid order by friendly_name ASC";
                }
                if(!$Equipment_List = mysql_query($Hardware_Query)){
                    error_log("Encountered Error: p2indexEP.php, Query HArdware_Query returned invalid result: ".mysql_error());
                }
                $BOOTH = 0;
                $hardware_number=0;
                $hardware_buffer="<span id=\"HDW_title_open\"><button onclick=\"HideHardware()\" title=\"Hide\"><span class=\"ui-icon ui-icon-carat-1-n\"></span></button><span>Hardware Control</span></span>";

                while($Equipment_row = mysql_fetch_array($Equipment_List)){
                    if($Equipment_row['ipv4_address']==$_SERVER['REMOTE_ADDR'] || $_SESSION['access']==2){
                        $hardware_number++;
                        /*echo "<script>
                        setInterval(function(){
                            Query_Device('RES".$Equipment_row['hardwareid']."','8','".$Equipment_row['hardwareid']."');
                            },'".$Equipment_row['hardwareid']."0000');</script>";*/
                        $hardware_buffer += "<hr><div id=\"toolbar".$Equipment_row['hardwareid']."\"  style=\"color: white; background:#000; width:100%; display:block\">
                        <span >".strtoupper($Equipment_row['Manufacturer'])." ".$Equipment_row['device_code']." - ".$Equipment_row['friendly_name']."</span><span style='width:100%'>&nbsp</span>
                        <span id='RES".$Equipment_row['hardwareid']."' style=\"color: white; background: #7690a3; width:100%; text-align: center; background-color: #7690a3;\">&nbsp;- DENON - </span>
                        <span id='RES".$Equipment_row['hardwareid']."-timer' style=\"color: white; background: #7690a3; width:100%; text-align: center; background-color: #7690a3;\"></span><br>
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
        <table style="background-color:white; width:1350px;">
        <tr><th style="width:8%">
        Air Date
        </th><th style="width:6%">
        Air Time
        </th><th style="width:14%">
        Program
        </th><th style="width:7%">
        Station
        </th><th style="width:58%">
        Description
        </th><th style="width:8%">
        Pre-Record
        </th><th style="width:5%">

        </th>
        </tr>
        
        <tr><td style="vertical-align:top">

	    
	    <?php echo $EPINFO['date']; ?>
        </td><td style="vertical-align:top">
	    
	    <?php echo $EPINFO['starttime']; ?>
        </td><td style="vertical-align:top">
             
             <?php echo $EPINFO['programname'];?>
        </td><td style="vertical-align:top">
             
             <?php echo $EPINFO['callsign'];?>
        </td><td style="vertical-align:top">
             
             <?php echo $EPINFO['description']; ?>
        </td><td style="vertical-align:top">
	    <?php 
	     	
	     	//chec if not enabled
	      if(!isset($_POST['enprerec']))
              {
                echo ' ';
              }
              else // if enabled execute
              {
                echo "<input type=\"date\" name=\"prerecord\" hidden=\"true\" value=\"" . $_POST['prdate'] . "\" />";
              }
			  
	    ?>

        </td></tr>
        <tr><td colspan="100%">
        	<hr>
            <div id="EAS">
                <?php /*require_once "../TPSBIN/XML/Emergency.php"*/ ?>
            </div>
        </td></tr>
        <!-- Row for displaying Ads and Friends -->
		<tr>
			<td colspan="2" style="color:green;">
				Available Friends Ads
			</td>
			<td colspan="2" style="color:blue;">
				<span title="NON OPTIONAL paid ads">Required Commercials This Hour</span>
			</td>
			<td colspan="2" style="color:orange;">
				<span title="Available Promos">Messages</span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<select name="adbox" id="friends" onchange="CHAVF()"><?php
					/*$REQAD_SQL = "select * from adrotation where '".date('H:i')."' between startTime and endTime and exists
					(select AdIdRef from addays where  Day='".date('l')."' and adrotation.RotationNum = addays.AdIdRef) and 
					(select count(songId) from song where song.title=(select AdName from adverts where adverts.AdId=adrotation.AdId)
					and song.time between adrotation.startTime and adrotation.endTime) < adrotation.BlockLimit";*/
					//$REQAD_SQL = "SELECT adverts.*,adrotation.* FROM adrotation,addays,adverts WHERE '".date('H').":00:00' BETWEEN adrotation.startTime AND adrotation.endTime AND addays.AdIdRef=adrotation.RotationNum AND adrotation.AdId=adverts.AdId AND addays.Day='".date('l')."' AND adverts.active='1'";
					$REQAD_SQL = "SELECT adverts.*,adrotation.* FROM adrotation,addays,adverts WHERE '".date('H:i:s')."' BETWEEN adrotation.startTime AND adrotation.endTime AND addays.AdIdRef=adrotation.RotationNum AND adrotation.AdId=adverts.AdId AND addays.Day='".date('l')."' AND adverts.active='1' AND '".date('Y-m-d')."' BETWEEN adverts.StartDate AND adverts.EndDate";
					
					$RQADSIDS = array();
					$REQAD = "";
					if(!$READS = mysql_query($REQAD_SQL))
					{
						$REQAD .= "<option value='-1'>ERROR - AdRotation</option>";
					}
					else if(mysql_num_rows($READS)==0){
						$REQAD .= "<option value='-1'>No Paid Commercials</option>";
					}
					else if(!isset($SPONS)){
						while($PdAds=mysql_fetch_array($READS)){
							if($PdAds['Limit'] == NULL || $PdAds['Playcount'] < $PdAds['Limit']){
								// Check BlockLimit (BLIM)
								$CHECKBLIM = "SELECT count(song.songid) FROM adrotation,song WHERE adrotation.AdId='".$PdAds['AdId']."' AND song.title='".$PdAds['AdName']."' and song.date='".$EPINFO['date']."' and song.time BETWEEN '".$PdAds['startTime']."' AND '".$PdAds['endTime']."' ";
								$BL_lim_R = mysql_query($CHECKBLIM);
								$BL_lim = mysql_fetch_array($BL_lim_R);
								if(mysql_error()){
									echo "<option value='-3'>ERROR SQL</option>";
								}
								if($BL_lim['count(song.songid)']<$PdAds['BlockLimit']){
									//echo "<option value='-2'>BL_Lim:".$BL_lim['count(song.songid)']."</option>";
									$REQAD .= "<option value='".$PdAds['AdId']."'>".$PdAds['AdName']."</option>";
									array_push($RQADSIDS,$PdAds['AdId']);
                                    array_push($ADIDS,$PdAds['AdId']);
                                    $SQL_PL_AD = "INSERT INTO promptlog (EpNum,AdNum) VALUES (".$EPINFO['EpNum'].",".$PdAds['AdId'].")";
                                    if(!mysql_query($SQL_PL_AD)){
                                        echo "<!-- ERROR: " . mysql_error() . "-->";
                                        error_log("TPS Error; Line 951: Could not perform SQL Query - ".mysql_error());
                                    }
                                    else{
                                        echo "<!-- Inserted into Log -->";
                                    }

								}	
							}
						}
						
						/*while($RQADS = mysql_fetch_array($READS)){
							if(!$ADINFOARR = mysql_query("select * from adverts where AdId='".$RQADS['AdId']."'")){
								$REQAD .= "<option value='-1'>ERROR - Adverts</option>";
							}
							else{
								$adinf = mysql_fetch_array($ADINFOARR);
								$HR = date('H');
								$HRN = $HR+1;
								$SQL_ADS_HR = "select count(songid) from song where category = '".$adinf['Category']."' and title='".$adinf['AdName']."' and time between '".$HR.":00' and '".$HRN.":00'";
								//echo $SQL_ADS_HR;
								if(!$adcount = mysql_query($SQL_ADS_HR)){
									$REQAD .= "<option value=\"-1\">ERROR - Hourly Limit</option>";//mysql_error();
								}
								$ADCO = mysql_fetch_array($adcount);
								if($ADCO['count(songid)']<$RQADS['HourlyLimit']){
									$ADCO['count(songid)'];
									//echo $RQADS['HourlyLimit'];
									echo "<option value=\"".$adinf['AdId']."\">".$adinf['AdName']."</option>";
									array_push($RQADSIDS,$adinf['AdId']);	
								}
								else{
									//echo "<option value='-1'>No Paid Commercials [#E2]</option>";
								}
								//echo "<option value=\"".$ADCO['count(songid)']."\">".$ADCO['count(songid)']."</option>";
							}
						}*/
					}

				// Friends Ads
				if(sizeof($RQADSIDS) > 0 && !isset($SPONS)){
					$ADOPT .= "<option>Paid Ad Required this hour [".sizeof($RQADSIDS)."]</option>";
				}
				else
				{
					if(isset($SPONS)){
						$ADOPT .= "<option value='".$SPONS['AdId']."'>".$SPONS['AdName']."</option>";
						array_push($ADIDS,$avadi['AdId']);
					}
					else{
						//$selcom51 is origin
						$minplaysql51 = "select MIN(Playcount) from adverts where Category='51' and Active='1' and Friend='1' ";
						if(!$minplay51Array = mysql_fetch_array(mysql_query($minplaysql51))){
							$selcom51 = "select * from adverts where Category='51'";
						}
						else{
							$minplay51 = $minplay51Array['MIN(Playcount)'];
							$selcom51 = "select * from adverts where Category='51' and '" . addslashes($_POST['user_date']) . "' between StartDate and EndDate and Friend='1' and Active='1' and Playcount='".$minplay51."' ";
							//echo $minplay51;
						}
						$selspon = "select MIN(Playcount) from adverts where Category!='51' and '" . addslashes($_POST['user_date']) . "' is between EndDate and StartDate ";
						
						if($comsav=mysql_query($selcom51)){
							$ADOPT = "";
							while($avadi = mysql_fetch_array($comsav)){
								$ADOPT .= "<option value=\"" . $avadi['AdId'] . "\">" . $avadi['AdName'] . "</option>";
								array_push($ADIDS,$avadi['AdId']);
							} 
						}
						else{
							$ADOPT = "<option value=\"-1\">ERROR - SQL Command</option>";
							//echo mysql_error(); 
						}
					}
				}
				echo $ADOPT;
					
				?>
				</select>
			</td>
			<td colspan="2">
				<select name="rqAds">
				<?php
        if(sizeof($RQADSIDS)>0){
				if($REQAD!=""&&!isset($SPONS)){
					echo $REQAD;
				}
                else if(isset($SPONS)){
                    echo "<option>Sponsored Program</option>";
                }
				else{
					echo "<option>No Required Ads [E3]</option>";
				}
        }
        else{
            echo "<option>No Required Ads</option>";
        }
				?>
				</select>
			</td>
			<td>
				<?php
					//echo sizeof($ADIDS);
				?>
				<span style="font-style: italic;color: #eb4b20"><strong>IMPORTANT: When prompted to play required ADs you are <u>ONLY</u> to play the required ADs until they are no longer prompted. Then, and only then are you permitted to play a friend ad <u>IF</u> you have not met your AD requirements</strong></span>
			</td>
			<td class="clock">
	            <ul style="margin:0 auto; padding:0px; list-style:none; text-align:center;">
		            <li id="hours" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;"><?php echo date("H");?></li>
		            <li id="point" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">:</li>
		            <li id="min" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;"><?php echo date("i");?></li>
		            <li id="point" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">:</li>
		            <li id="sec" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;"><?php echo date("s");?></li>
	            </ul>
            </td>
		</tr>
        
        <tr> <!-- This is the second Row Of Data -->

        <!-- horizontal line -->
        <td colspan="7">
        <hr />
        </td></tr><!-- End Second Row -->



        <!-- Third Row (Song Data) of Data

        So Stuff Gets A Little complicated here,
        Using a PHP Generated HTML Table data
        already entered is listed but first there
        must be the working row as this should be
        on the top for ease of access. plus the rows
        below will then be modifiable as we can end
        the form tag and have individual update tags
        for the rows. this may get ugly...
        -->
        <!--<tr>-->
        </table>
        <div id="Alert" class="ui-state-error" style="width: 1350px; text-align: center; display: none;"></div>
         <div id="processing" style="width: 1350px; background-color: white; text-align: center; float: left; display: none">
        			<!--<img src="/images/GIF/spinner.gif" height="50px" alt="Processing"/>-->
        			<img src="../images/GIF/ajax-loader2.gif" alt="..."/><span>Processing...</span>
        </div>
        
        <!--/////////////////////////// Input Form (Advertisements) //////////////////////////-->
        	<form name="formad" method="post" id="frm1" action="p2insertEP.php" onsubmit="formsubmit()">  
        	<input type="date" name="user_date" hidden value=<?php echo "\"" . $_POST['user_date'] . "\"";?>/>
        	<input type="time" name="user_time" hidden value=<?php echo "\"" . $_POST['user_time'] . "\"";?>/>
        	<input type="text" name="program" hidden value=<?php echo "\"" . $_POST['program'] . "\"";?> />
        	<input type="text" name="station" hidden value=<?php echo "\"" . $CALLSHOW . "\"";?> />
        	<input type="text" name="description" hidden value=<?php echo "\"" . $DESCRIPTION . "\""; ?> />
        	<input type="hidden" name="artist" hidden value=<?php echo "\"" . $CALLSHOW . "\""; ?> />
        	<input type="hidden" name="album" hidden value="Advertisement" />
        	<!--<input type="text"-->
        	  
        	   <!-- //// END FORM DEFINITION //// --> 
        <div id="InputAdvert" style="width: 100%; text-align: center; display: none;">
            <table style="width: 1350px; vertical-align: top; background-color:white;">
                  <tr><!-- Header Definitions for Advertisements -->
                  	
                       <th style="width:5%">
                           Type  <input type="button" value="Define" onclick="return popitup('../help/definetype.html')"/>
                       </th>
                       <th style="width:5%" id="Adnumer">
                           Identifier
                       </th>
                       <th id="Adtime">
                           Time
                       </th>
                       <th id="Adname">
                           All Commercials
                       </th colspan="100%">
                       <!--<th id="arHead">
                           Artist
                       </th>
                       <th>
                           Album (Release Title)
                       </th>
                       
                       <th width="2%">
                           CC
                       </th>
                       <th width="2%">
                           Hit
                       </th>
                       <th width="2%">
                           Ins
                       </th>
                       <th width="5 %">
                           Language
                       </th>
                       <th width="5%">-->

                       </th>
              </tr>
              <tr ><!-- Blank Row for song insertion -->
                       <td>
                           <select name="cat" id="DDLAdvert" onchange="UnCHtype()">
                                   <!--
                                   <OPTION VALUE="5">5, Commercial</OPTION>
                                   <OPTION VALUE="4">4, Musical Production</option>
                                   <OPTION VALUE="3">3, Special Interest</option>
                                   <OPTION VALUE="2" selected="selected">2, Popular Music</option>
                                   <option value="1">1, Spoken</option>
                                   -->
                                   <!-- Using Sub Categories -->
                                   
                                   <option value="53">53, Sponsored Promotion</option>
                                   <OPTION value="52">52, Sponsor Indentification</OPTION>
                                   <OPTION VALUE="51">51, Commercial</OPTION>
                                   <option value="45">45, Show Promo</option>
                                   <option value="44">44, Programmer/Show ID</option>
                                   <option value="43">43, Musical Station ID</option>
                                   <option value="42">42, Tech Test</option>
                                   <option value="41">41, Themes</option>
                                   	<option value="36">36, Experimental</option>
                                   	<option value="35">35, NonClassical Religious</option>
                                   	<option value="34">34, Jazz and Blues</option>
                                   	<option value="33">33, World/International</option>
                                   	<option value="32">32, Folk</option>
                                   	<option value="31">31, Concert</option>
                                   	<!--<option value="3">3, Special Interest</option>-->
                                   	<option value="24">24, Easy Listening</option>
                                   	<option value="23">23, Acoustic</option>
                                   	<option value="22">22, Country</option>
                                   	<option value="21">21, Pop, Rock and Dance</option>
                                   	<!--<option value="2" selected="True">2, Popular Music</option>-->
                                   <option value="12">12, PSA/Spoken Word Other</option>
                                   <OPTION VALUE="11">11, News</option>

                           </select>
                       </th>
                       <th id="plcont">
                           <input type="text" id="AdNum" name="AdNum" readonly="true" size="10"/>
                       </th>
                       <th>
                           <input type="time" id="ins_time" name="time" value=<?php
                           if(isset($_POST['ENPREC']) || isset($EPINFO['prerecorddate'])){
                           	if(isset($EPINFO['endtime'])){
	                             echo "\"" . $EPINFO['endtime'] . "\"";
	                           }
	                           else if(isset($_POST['time'])){
	                             echo "\"" . $_POST['time'] . "\"";
	                           }
	                           else{
	                             echo "\"" . $_POST['user_time'] . "\"";
	                           }
						   }
							else{
								echo "\"" . date('H:i') . "\" ";
							}
                             ?>/>
                       </th>
                       <th>
                           <?php 
                           //<input type="text" name="title" id="title001" size="33" required="true" maxlength="45">
                           	echo "<select id=\"ADLis\" name=\"title\" onChange=\"ADCH()\" >";
								$SLADS = "select * from adverts where Category='51' and '" . addslashes($_POST['user_date']) . "' between StartDate and EndDate and Active='1' order by AdName";
                           		if(!$SRZ = mysql_query($SLADS)){
                           			echo "<option value='0'>NO ADS AVAILABLE</option>";
                           		}
								else{
                                    $ADGR_AVAIL = array();
                                    $ADGR_REQUI = array();
                                    $ADGR_INVAL = array();
									while($ADZL=mysql_fetch_array($SRZ)){
                                        $AVAIL=FALSE;
                                        $REQUIRE=FALSE;
										$TEMP = "<option value=\"" . $ADZL['AdId'] . "\" ";
										if(in_array((int)$ADZL['AdId'], $ADIDS)){
                                            $AVAIL = TRUE;
											$TEMP .= " style=\"background-color:green; color:white\" ";
										}
										else if((int)in_array($ADZL['AdId'], $RQADSIDS)){
                                            $REQUIRE = TRUE;
											$TEMP .= " style=\"background-color:blue; color:white\" ";
										}
										$TEMP .= " >". $ADZL['AdName'] ."</option>";

                                        if($REQUIRE){
                                            array_push($ADGR_REQUI,$TEMP);
                                            echo "<!-- Entered Require -->";
                                        }
                                        elseif($AVAIL){
                                            array_push($ADGR_AVAIL,$TEMP);
                                        }
                                        else{
                                            array_push($ADGR_INVAL,$TEMP);
                                        }
									}
								}
                            echo "<optgroup label='Required Advertisements";
                                if(empty($ADGR_REQUI)){
                                    if(sizeof($ADGR_REQUI)<sizeof($RQADSIDS)){
                                        echo " (DIFF-OVERRIDE) [".sizeof($ADGR_REQUI)."/".sizeof($RQADSIDS)."]'>";
                                        echo $REQAD;
                                        error_log("TPS Error, Could not account for required Adverts, possible code error values ".var_dump($RQADSIDS)." ");
                                    }
                                    else{
                                        echo " (None) [".sizeof($ADGR_REQUI)."/".sizeof($RQADSIDS)."]'>";
                                    }
                                }
                                else{
                                    echo "'>";
                                    foreach ($ADGR_REQUI as $opt){
                                        echo $opt;
                                    }
                                }
                            echo "</optgroup>";
                            echo "<optgroup label='Available Advertisements";
                                if(empty($ADGR_AVAIL)){
                                    echo " (None)'>";
                                }
                                else{
                                    echo "'>";
                                    foreach ($ADGR_AVAIL as $opt){
                                        echo $opt;
                                    }
                                }
                            echo "</optgroup>";
                            echo "<optgroup label='Invalid Advertisements";
                                if(empty($ADGR_INVAL)){
                                    echo " (None)'>";
                                }
                                else{
                                    echo "'>";
                                    foreach ($ADGR_INVAL as $opt){
                                        echo $opt;
                                    }
                                }
                            echo "</optgroup>";
                           	echo "</select>";
                           ?>
                       </th>
                       <th>
                           <input name="sub" id="sub_ann" type="submit" value="Insert" onclick="formsubmit()"/>
                           </form>
                       </th>
                       <th colspan="100%">
                       </th>
              </tr>
              </table>
              </div>
        
         <!--/////////////////////////// Input Form (Sponsor 53) //////////////////////////-->
        	<!--<form name="formad" method="post" id="frm3" action="p2insertEP.php" onsubmit="formsubmit()">
        	<input type="date" name="user_date" hidden value=<?php echo "\"" . $_POST['user_date'] . "\"";?>/>
        	<input type="time" name="user_time" hidden value=<?php echo "\"" . $_POST['user_time'] . "\"";?>/>
        	<input type="text" name="program" hidden value=<?php echo "\"" . $_POST['program'] . "\"";?> />
        	<input type="text" name="station" hidden value=<?php echo "\"" . $CALLSHOW . "\"";?> />
        	<input type="text" name="description" hidden value=<?php echo "\"" . $DESCRIPTION . "\""; ?> />
        	<input type="text" name="artist" hidden="true" value=<?php echo "\"" . $CALLSHOW . "\""; ?> />
        	<input type="text" name="album" hidden="true" value="Advertisement" />-->
        	<!--<input type="text"-->
        	  
        	   <!-- //// END FORM DEFINITION //// --> 
        <!--<div id="InputSponsor" style="width: 100%; text-align: center; display: none;">
            <table colspan="7" width="1350" valign="top" style="background-color:white;">
                  <tr>--><!-- Header Definitions for Advertisements -->
                  	<!--<th colspan="7"><span>Category 53 is for sponsored promotion recording only (not friends, they are 51)</span></th></tr><tr>
                       <th width="5%">
                           Type  <input type="button" value="Define" onclick="return popitup('../help/definetype.html')"/>
                       </th>
                       <th width="5%" id="Adnumer">
                           Identifier
                       </th>
                       <th id="Adtime">
                           Time
                       </th>
                       <th id="Adname">
                           All Sponsors
                       </th colspan="100%">
 
                       </th>
              </tr>
              <tr >--><!-- Blank Row for song insertion -->
                       <!--<td>
                           <select name="cat" id="DDLAdvert" onchange="UnCHtype()">
                                   
                                   <option value="53">53, Sponsored Promotion</option>
                                   <OPTION value="52">52, Sponsor Indentification</OPTION>
                                   <OPTION VALUE="51">51, Commercial</OPTION>
                                   <option value="45">45, Show Promo</option>
                                   <option value="44">44, Programmer/Show ID</option>
                                   <option value="43">43, Musical Station ID</option>
                                   <option value="42">42, Tech Test</option>
                                   <option value="41">41, Themes</option>
                                   	<option value="36">36, Experimental</option>
                                   	<option value="35">35, NonClassical Religious</option>
                                   	<option value="34">34, Jazz and Blues</option>
                                   	<option value="33">33, World/International</option>
                                   	<option value="32">32, Folk</option>
                                   	<option value="31">31, Concert</option>
                                   	<option value="24">24, Easy Listening</option>
                                   	<option value="23">23, Acoustic</option>
                                   	<option value="22">22, Country</option>
                                   	<option value="21">21, Pop, Rock and Dance</option>
                                   <option value="12">12, PSA/Spoken Word Other</option>
                                   <OPTION VALUE="11">11, News</option>

                           </select>
                       </th>
                       <th id="plcont">
                           <input type="text" id="AdNum" name="AdNum" readonly="true" size="10"/>
                       </th>
                       <th>
                           <input type="time" name="time" value=<?php
                           if(isset($_POST['ENPREC']) || isset($EPINFO['prerecorddate'])){
                           	if(isset($EPINFO['endtime'])){
	                             echo "\"" . $EPINFO['endtime'] . "\"";
	                           }
	                           else if(isset($_POST['time'])){
	                             echo "\"" . $_POST['time'] . "\"";
	                           }
	                           else{
	                             echo "\"" . $_POST['user_time'] . "\"";
	                           }
						   }
							else{
								echo "\"" . date('H:i') . "\" ";
							}
                             ?>/>
                       </th>
                       <th>
                           <?php 
                           //<input type="text" name="title" id="title001" size="33" required="true" maxlength="45">
                           	echo "<select id=\"ADLis\" name=\"title\" onChange=\"ADCH()\" >";
								$SLADS = "select * from adverts where Category='53' and '" . addslashes($_POST['user_date']) . "' between StartDate and EndDate and Active='1' order by AdName";
                           		if(!$SRZ = mysql_query($SLADS)){
                           			echo "<option value='0'>NO ADS AVAILABLE</option>";
                           		}
								else{
									while($ADZL=mysql_fetch_array($SRZ)){
										echo "<option value=\"" . $ADZL['AdId'] . "\" ";
										if(in_array($ADZL['AdId'], $ADIDS)){
											echo " style=\"background-color:green; color:white\" ";
										}
										else if(in_array($ADZL['AdId'], $RQADSIDS)){
											echo " style=\"background-color:blue; color:white\" ";
										}
										echo " >". $ADZL['AdName'] ."</option>
				
										";
									}
								}
                           	echo "</select>";
                           ?>
                       </th>
                       <th>
                           <input name="sub" type="submit" value="Insert" onclick="formsubmit()"/>
                           </form>
                       </th>
                       <th colspan="100%">
                       </th>
              </tr>
              </table>
              </div>
        -->
        
        <!--/////////////////////////// Input Form (regular) //////////////////////////-->
        	<form name="form1" method="post" id="frm2" action="p2insertEP.php" onsubmit="formsubmit()">  
        	<input type="date" name="user_date" hidden value=<?php echo "\"" . $_POST['user_date'] . "\"";?>/>
        	<input type="time" name="user_time" hidden value=<?php echo "\"" . $_POST['user_time'] . "\"";?>/>
        	<input type="text" name="program" hidden value=<?php echo "\"" . $_POST['program'] . "\"";?> />
        	<input type="text" name="station" hidden value=<?php echo "\"" . $CALLSHOW . "\"";?> />
        	<input type="text" name="description" hidden value=<?php echo "\"" . $DESCRIPTION . "\""; ?> />
        	   <!-- //// END FORM DEFINITION //// --> 
        <div id="inputdiv" style="width: 100%; text-align: center; ">
            <table colspan="7" width="1350" valign="top" style="background-color:white;">
                  <tr><!-- Header Definitions for songs -->
                  	
                       <th width="5%">
                           Type  <input type="button" value="Define" onclick="return popitup('../help/definetype.html')"/> <input type="button" value="Notes" name="NButton" onclick="GetNotes();" />
                       </th>
                       <th width="5%" id="plhead" onchange="fetchplaylist()">
                           Playlist
                       </th>
                       <th width="5%" id="spokenc" style="display: none">
                       		Minutes
                       	</th>
                       <th>
                           Time
                       </th>
                      <?php if($EPINFO['Display_Order']==0){
                            echo "<th style=\"width:40px;\">Title</th><th id=\"arHead\">Artist</th><th>Album (Release Title)</th>";
                        }
                        elseif($EPINFO['Display_Order']==1){
                            echo "<th style=\"width:40px;\"id=\"arHead\">Artist</th><th>Album (Release Title)</th><th>Title</th>";
                        }
                        else{
                            echo "<th>Title</th><th id=\"arHead\">Artist</th><th>Album (Release Title)</th>";
                        }
                       
                       ?>
                       <th>
                           Composer
                       </th>
                       <th width="2%">
                           CC
                       </th>
                       <th width="2%">
                           Hit
                       </th>
                       <th width="2%">
                           Ins
                       </th>
                       <th width="5 %">
                           Language 
                       </th>
                       <th width="5%">
							<input type="hidden" name="note" id="NF1"/>
							
                       </th>
              </tr>
              <tr><!-- Blank Row for song insertion -->
                       <td>
                           <select name="cat" id="DDLNormal" onchange="CHtype()">
                                   <!--
                                   <OPTION VALUE="5">5, Commercial</OPTION>
                                   <OPTION VALUE="4">4, Musical Production</option>
                                   <OPTION VALUE="3">3, Special Interest</option>
                                   <OPTION VALUE="2" selected="selected">2, Popular Music</option>
                                   <option value="1">1, Spoken</option>
                                   -->
                                   <!-- Using Sub Categories -->
                                   
                                   <option value="53">53, Sponsored Promotion</option>
                                   <OPTION value="52">52, Sponsor Indentification</OPTION>
                                   <OPTION VALUE="51">51, Commercial</OPTION>
                                   <option value="45">45, Show Promo</option>
                                   <option value="44">44, Programmer/Show ID</option>
                                   <option value="43">43, Musical Station ID</option>
                                   <option value="42">42, Tech Test</option>
                                   <option value="41">41, Themes</option>
                                   	<option value="36">36, Experimental</option>
                                   	<option value="35">35, NonClassical Religious</option>
                                   	<option value="34">34, Jazz and Blues</option>
                                   	<option value="33">33, World/International</option>
                                   	<option value="32">32, Folk</option>
                                   	<option value="31">31, Concert</option>
                                   	<!--<option value="3">3, Special Interest</option>-->
                                   	<option value="24">24, Easy Listening</option>
                                   	<option value="23">23, Acoustic</option>
                                   	<option value="22">22, Country</option>
                                   	<option value="21">21, Pop, Rock and Dance</option>
                                   	<!--<option value="2" selected="True">2, Popular Music</option>-->
                                   <option value="12">12, PSA/Spoken Word Other</option>
                                   <OPTION VALUE="11">11, News</option>

                           </select>
                       </th>
                       <th id="plbody">
                           <input type="number" name="playlist" style="width: 50px;" maxlength="4" min="0" onmousewheel="javascript: return false">
                       </th>
                       <th id="spokcon" style="display:none;">
                       		<input type="number" step="0.25" name="spokenmin" style="width: 50px; color: green; box-shadow: inset;" size="5" max="480" min="0" onmousewheel="javascript: return false">
                       </th>
                       <th>
                           <input type="time" name="time" value=<?php
                           if(isset($_POST['ENPREC']) || isset($EPINFO['prerecorddate'])){
                           	if(isset($EPINFO['endtime'])){
	                             echo "\"" . $EPINFO['endtime'] . "\"";
	                           }
	                           else if(isset($_POST['time'])){
	                             echo "\"" . $_POST['time'] . "\"";
	                           }
	                           else{
	                             echo "\"" . $_POST['user_time'] . "\"";
	                           }
						   }
							else{
								echo "\"" . date('H:i') . "\" ";
							}
                             ?> onmousewheel="javascript: return false"/>
                       </th>
                       <th>
                       <?php
                           if($EPINFO['Display_Order']==0){
                               echo"<input type=\"text\" name=\"title\" id=\"title001\" size=\"25\" required maxlength=\"90\" placeholder=\"Title\">
                           <input list=\"spoken\" name=\"title\" id=\"data1\" size=\"25\" disabled required  maxlength=\"90\" style=\"display:none\" value=\"Spoken Word / Talk\"/>
                           <datalist id=\"spoken\">
                           		<option value=\"Spoken Word / Talk\">
                           		<option value=\"PSA / Promo\">
                           		<option value=\"News\">
                           		<option value=\"Verbal Station ID\">
                           </datalist></th><th>
                           <input type=\"text\" id=\"artin\" name=\"artist\" size=\"25\" maxlength=\"90\" placeholder=\"Artist\"/>
                       </th><th>
                           <input type=\"text\" id=\"albin\" name=\"album\" size=\"25\" maxlength=\"90\" placeholder=\"Album\"/>
                       </th>";
                           }
                           else if($EPINFO['Display_Order']==1){
                               echo"<input type=\"text\" id=\"artin\" name=\"artist\" size=\"25\" maxlength=\"90\" placeholder=\"Artist\"/>
                       </th><th>
                           <input type=\"text\" id=\"albin\" name=\"album\" size=\"25\" maxlength=\"90\" placeholder=\"Album\"/>
                       </th><th>
                       <input type=\"text\" name=\"title\" id=\"title001\" size=\"25\" required maxlength=\"90\" placeholder=\"Title\"/>
                           <input list=\"spoken\" name=\"title\" id=\"data1\" size=\"25\" disabled required  maxlength=\"90\" style=\"display:none\" value=\"Spoken Word / Talk\"/>
                           <datalist id=\"spoken\">
                           		<option value=\"Spoken Word / Talk\">
                           		<option value=\"PSA / Promo\">
                           		<option value=\"News\">
                           		<option value=\"Verbal Station ID\">
                           </datalist></th>";
                           }
                           else{
                               echo"<input type=\"text\" name=\"title\" id=\"title001\" size=\"25\" required maxlength=\"90\">
                           <input list=\"spoken\" name=\"title\" id=\"data1\" size=\"25\" disabled required  maxlength=\"90\" style=\"display:none\"/>
                           <datalist id=\"spoken\">
                           		<option value=\"Spoken Word / Talk\">
                           		<option value=\"PSA / Promo\">
                           		<option value=\"News\">
                           		<option value=\"Verbal Station ID\">
                           </datalist></th><th>
                           <input type=\"text\" id=\"artin\" name=\"artist\" size=\"25\" maxlength=\"90\"/>
                       </th><th>
                           <input type=\"text\" id=\"albin\" name=\"album\" size=\"25\" maxlength=\"90\"/>
                       </th>";
                           }
                       ?>    
                           
                       <th>
                           <input type="text" id="composer" name="composer" size="25" maxlength="90" placeholder="Composer"/>
                       </th>
                       <th>
                           <input type="checkbox" id="ccin" name="cancon" value="1"/>
                       </th>
                       <th>
                           <input type="checkbox" id="hitin" name="hit" value="1"/>
                       </th>
                       <th>
                           <input type="checkbox" id="insin" name="instrumental" value="1"/>
                       </th>
                       <th>
                           <input list="lang" name="lang" required value="English" size="10" maxlength="40"/>
                           <datalist id="lang">
                           		<option value="English">
                           		<option value="French">
                           </datalist>
                       </th>
                       <th>
                           <input name="sub" type="submit" value="Insert" onclick="formsubmit()"/>
                           </form>
                       </th>
              </tr>
              </table>
    <div id="info_player" <?php if($_SESSION['access']<2){print("style=\"display:none;\"");}?>>
    </div>
              </div>
              <table style="width:1350px; vertical-align:top; background-color:white;">
              	<thead style="width:1350px;">
                      <tr><td colspan="100%">Recorded Information</td></tr>
                      <tr><th style="width:70px">Category</th><th style="width:50px">Playlist</th><th style="width:50px">Spoken</th><th style="width:60px">Time</th>
                      <?php 
                            if($EPINFO['Display_Order']==0){
                                echo "<th width=\"230px\">Title</th><th width=\"230px\">Artist</th><th width=\"230px\">Album</th>";
                            }
                            else if ($EPINFO['Display_Order']==1){
                                echo "<th width=\"230px\">Artist</th><th width=\"230px\">Album</th><th width=\"230px\">Title</th>";
                            }

                        ?>
                      <th style="width:250px;">Composer</th><th style="width:20px;">CC</th><th width="20px">Hit</th><th width="20px">Ins</th><th width="200px">Language</th></tr></thead>
               <tbody class="striped"><tr> <!-- Row for displaying already entered data -->
                   <?php
                   	$ORDERque = "select displayorder from program where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' ";
					$ORDER = mysql_fetch_array(mysql_query($ORDERque));
                    $query = "select * from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' order by time " . $ORDER['displayorder'] .", songid " . $ORDER['displayorder'];
                    $listed=mysql_query($query,$con);
                     if(mysql_num_rows($listed)=="0"){
                       echo "<tr><td colspan=\"100%\" style=\"background-color:".$SETTINGS['ST_ColorFail'].";\">No Songs Recorded Yet</td></tr>";
                     }
					 else if(mysql_errno())
					 {
					 	echo mysql_error();
					 }
                     else
                     {
                         while ($list=mysql_fetch_array($listed))
                         {
                           echo "<tr>";
                           echo "<td>";
                                echo $list['category'];
							/*if($list['AdViolationFlag']=='1'){
						   		echo "<img src=\"/images/ICONS/ERROR.PNG\" alt=\"notice\" height=\"15px\" width=\"15px\"
						   		onclick=\"alert('Notice \\n \\nThis ad was not listed in the available friends list, \\nthis will not be counted toward your requirements\\n\\nplease only play ads from the required and available friends lists\\n This Ad's Priority has been decreased')\" />";	
						   	}*/
                           echo "</td><td>";
                                echo $list['playlistnumber'];
                           echo "</td><td>";
						   echo $list['Spoken'];
                           echo "</td><td>";
                                echo $list['time'];
                           echo "</td><td>"; /// Artist - Album - Title
                           if($EPINFO['Display_Order']==0){
                                echo $list['title'];
                           echo "</td><td>";
                                echo $list['artist'];
                           echo "</td><td>";
                                echo $list['album'];
                           echo "</td><td>";
                           }
                           else if($EPINFO['Display_Order']==1){
                                echo $list['artist'];
                           echo "</td><td>";
                                echo $list['album'];
                           echo "</td><td>";
                                echo $list['title'];
                           echo "</td><td>";
                           }
                           else{
                                echo $list['title'];
                           echo "</td><td>";
                                echo $list['artist'];
                           echo "</td><td>";
                                echo $list['album'];
                           echo "</td><td>";
                           }
						   		echo $list['composer'];
                           if($list['category']=="51"){
                               print("</td><td><span class=\"ui-icon ui-icon-minus\"></span></td><td><span class=\"ui-icon ui-icon-minus\"></span></td><td><span class=\"ui-icon ui-icon-minus\"></span>");
                           }
                           else{
                               echo "</td><td>";
                           
                                   if($list['cancon']==1){
                                       echo "<span class=\"ui-icon ui-icon-check\"></span>";
                                   }
                                   else if($list['cancon']==0){
                                       // do nothing
                                   }
                                   else{
                                       echo "<span class=\"ui-icon ui-icon-notice\"></span>";
                                   }
                               echo "</td><td>";
                                    if($list['hit']==1){
                                       echo "<span class=\"ui-icon ui-icon-check\"></span>";
                                   }
                                   else if($list['hit']==0){
                                       // do nothing
                                   }
                                   else{
                                       echo "<span class=\"ui-icon ui-icon-notice\"></span>";
                                   }
                               echo "</td><td>";
                                    if($list['instrumental']==1){
                                       echo "<span class=\"ui-icon ui-icon-check\"></span>";
                                   }
                                   else if($list['instrumental']==0){
                                       // do nothing
                                   }
                                   else{
                                       echo "<span class=\"ui-icon ui-icon-notice\"></span>";
                                   }
                           }
                           $songlang = mysql_query("select languageid from LANGUAGE where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and songid='". addslashes($list['songid']) ."'");
                           $rowlang = mysql_fetch_array($songlang);
                           echo "</td><td>";
                                echo $rowlang['languageid'];
                           echo "</td>";
                           echo "</tr>";
						   if(isset($list['note'])){
						   	echo "<tr  style=\"background-color:".$SETTINGS['ST_ColorNote']."\"><td colspan=\"100%\">".$list['note']."</td></tr>";
						   }
                         }
                     }

                   ?>
               </tr>
        </tr>
        <tfoot>
        <form id="Complete" name="Complete" method="POST" action="p3insertEP.php">
        <tr>
        <td colspan="12" height="10">
        <hr />
        </td>
        </tr>
        <tr>
        <th colspan="2">
        <!--Status-->
        </th>
        <th colspan="2">
        Calculated Spoken Time
        </th>
        <th colspan="2">
        Time Complete
        </th>
        </tr>
        <tr>
        
        	<?php
        	if(!isset($EPINFO['endtime'])){
        		echo "<td colspan=\"2\" style=\"background-color:white; color:darkred;\"><span>Active:<br>Not Finalized</span>";
        	}
            elseif(!isset($EPINFO['EndStamp'])){
                echo "<td colspan=\"2\" style=\"background-color: #BB6599; color: black;\"><span>Complete:<br>Finalized - No Audit</span>";
            }
            elseif(strtotime($EPINFO['EndStamp'])>strtotime('yesterday')){
                echo "<td colspan=\"2\" style=\"background-color: #FFFF00; color: black;\"><span>Complete:<br>Finalized - Editable</span>";
            }
			else{
				echo "<td colspan=\"2\" style=\"background-color:red; color:white;\"><span>Complete:<br>Finalized - Locked</span>";
			}
        	?>
        </td>
        <td colspan="2">
        <input type="text" name="spoken" value=<?php
                           if(isset($EPINFO['totalspokentime'])){
                             echo "\"" . $EPINFO['totalspokentime'] . "\" readonly=\"true\"";
                           }
                           else{
                           	$SUMAR = "select sum(Spoken) from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' order by time desc";
							   if($spokensum = mysql_fetch_array(mysql_query($SUMAR))){
							   	if($spokensum['sum(Spoken)'] > 0){
                           			echo " \"".$spokensum['sum(Spoken)'] . "\" readonly=\"true\"";
								}
								else{
									echo "\"0\" style=\"color:red;\" readonly=\"true\"";
								}
							   }
							   else{
							   	echo "\"0\"";
							   }
                             //echo "\"0\"";
                           }
                             ?>/>
        </td>
        <td colspan="1">
        <input id="end_time" type="time" name="end" required value=<?php
                        
                        if(isset($_POST['ENPREC']) || isset($EPINFO['prerecorddate'])){
                           	if(isset($EPINFO['endtime'])){
	                             $END_TIME_VAL = "\"" . $EPINFO['endtime'] . "\"";
	                           }
	                           else if(isset($_POST['time'])){
	                             $END_TIME_VAL =  "\"" . $_POST['time'] . "\"";
	                           }
	                           else{
	                             $END_TIME_VAL = "\"" . $_POST['user_time'] . "\"";
	                           }
					    }
						else{
                            if(isset($EPINFO['endtime'])){
                                $END_TIME_VAL = "\"" . $EPINFO['endtime'] . "\"";
                            }
							else{
                                $END_TIME_VAL = "\"" . date('H:i') . "\" ";
                            }
						}
                        echo $END_TIME_VAL;
                             ?>/>
        </td>
        <td colspan="1">
        <input type="text" hidden name="callsign" value=<?php echo "\"" . $CALLSHOW . "\"" ?> />
        <input type="text" hidden name="program" value=<?php echo "\"" . $_POST['program'] . "\"" ?> />
        <input type="text" hidden name="user_date" value=<?php echo "\"" . $_POST['user_date'] . "\"" ?> />
        <input type="text" hidden name="user_time" value=<?php echo "\"" . $_POST['user_time'] . "\"" ?> />
        <button type='button' id='confirm_final'>Finalize Episode</button>
        <div id="dialog-form" title="Confirm Finish Episode">
          <p class="validateTips">Please confirm a finalization time.<br><br></p>
          <fieldset>
            <label for="name">Finalization Time</label>
            <input type="time" name=time_final_confirm" id="time_final_confirm" required class="text ui-widget-content ui-corner-all" value=<?php print($END_TIME_VAL);?> >
          </fieldset>
        </div>
        </td>
        </tr>
        </form>
        <tr><td colspan="12" style="height:20px;">
        <hr />
        </td></tr>
        <tr>
        <?php
            echo "<form name=\"exit\" action=\"../\" method=\"POST\" ";
            if(!isset($EPINFO['endtime'])){
            	echo " onSubmit=\"return confirm('WARNING: Unfinalized Episode\\n\\nThis episode is not finalized. Are you sure you want to exit?')\">";
            }
			else{
				echo "\">";
			}
                  echo "<td colspan=\"1\">
                  <input type=\"text\" hidden=\"true\" name=\"callsign\" value=\"" . $CALLSHOW . "\" />
                  <input type=\"text\" hidden=\"true\" name=\"program\" value=\"" . $_POST['program'] . "\"/>
                  <input type=\"text\" hidden=\"true\" name=\"user_date\" value=\"" . $_POST['user_date'] . "\"/>
                  <input type=\"text\" hidden=\"true\" name=\"user_time\" value=\"" . $_POST['user_time'] . "\"/>
                  <input type=\"submit\" value=\"Exit\" /></form>
                  </td><td></td>";
          
        ?>
        <td colspan="1">
        <!--<form name="exit" action="/Episode/p3update.php" method="POST">-->
        <form name="edit" action="EPV2/p3update.php" method="POST">
        <input type="hidden" name="callsign" value=<?php echo "\"" . $CALLSHOW . "\"" ?> />
        <input type="hidden" name="program" value=<?php echo "\"" . $_POST['program'] . "\"" ?> />
        <input type="hidden" name="user_date" value=<?php echo "\"" . $_POST['user_date'] . "\"" ?> />
        <input type="hidden" name="user_time" value=<?php echo "\"" . $_POST['user_time'] . "\"" ?> />
        <input type="submit" value="Edit" />
        </form>
        </td>
        <td colspan="1">
        <form name="refresh" action="p2insertEP.php" method="POST">
        <input type="hidden" name="callsign" value=<?php echo "\"" . $CALLSHOW . "\"" ?> />
        <input type="hidden" name="program" value=<?php echo "\"" . $_POST['program'] . "\"" ?> />
        <input type="hidden" name="user_date" value=<?php echo "\"" . $_POST['user_date'] . "\"" ?> />
        <input type="hidden" name="user_time" value=<?php echo "\"" . $_POST['user_time'] . "\"" ?> />
        <input type="submit" value="Refresh" />
        </form>
        </td>
        <td colspan="7">
            
        </td><td>
        <!--<img src="../images/mysqls.png" alt="MySQL Powered" />-->
        </td></tr>
        <?php 
        $PROML = mysql_query("SELECT count(*) AS Result FROM PromptLog WHERE EpNum='".addslashes($EPINFO['EpNum'])."' ");
        $PROMPTS = mysql_fetch_array($PROML);
            if($_SESSION['access']=='2'){
                if(isset($_POST['IPOR'])){
                    $LOCATION = addslashes($_POST['IPOR']);
                }
                else{
                    $LOCATION="";
                }
                if(empty($LOCATION)){
                    $LOCATION = $_SERVER['REMOTE_ADDR'];
                }
                $QUERY_HWD = "SELECT count(*) AS hardware FROM hardware WHERE ipv4_address='$LOCATION' and in_service='1' and station='".$EPINFO['callsign']."'";
                $Equipment = mysql_fetch_array(mysql_query($QUERY_HWD));

                echo "<tr style=\"background-color:#FFD633;\"><td colspan='2'>ADMINISTRATOR ACCESS</td><td colspan='2'>EPISODE: ".$EPINFO['EpNum']."</td><td colspan='1'>Prompt Records: ".$PROMPTS['Result']."</td>
                <td><a href='javascript:void(0)'>Hardware Count: ".$Equipment['hardware']."</a></td>
                </tr>";
            }
        ?>
                  </tfoot>
        </table>
    <div style="height: 30px;">&nbsp;</div>
    <div style="position: fixed; bottom: 0; height: 20px; width: 100%; background-color: #000; color: #fff;  margin: 0 0 0 0; vertical-align: bottom; box-shadow: 0px 0px 30px #000;">
        <span>Current Song (RDS): </span>
        <span id="current_song" style="color: #808080">Loading RDS Data...</span>
        <?php
            echo "<span style='float:right; padding-right: 10px'>Terminal: ".$_SERVER['REMOTE_ADDR']."</span>";
        ?>
    </div>
</body>
</html>
