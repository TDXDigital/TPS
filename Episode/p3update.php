<?php
      session_start();

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');}
}
else{
	echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
}


        $PROGRAMQUERY = "select * from episode where callsign=\"". addslashes($_POST['callsign']) ."\" and programname=\"" . addslashes($_POST['program']) . "\" and date=\"". addslashes($_POST['user_date']) ."\" and starttime=\"". addslashes($_POST['user_time'])."\"";
        $PROGRAMDATA = mysql_query($PROGRAMQUERY);
        $PROGRAMARRAY = mysql_fetch_array($PROGRAMDATA);
		$CALLSHOW = addslashes($_POST['callsign']);
        if( mysql_num_rows($PROGRAMDATA) == "0" )
        {
          echo "<table width=\"100%\" style=\"background-color:yellow;\"><tr><td>Notice: No Shows Match Given Data, or No Data Provided </td></tr></table>";
        }
        else if(isset($_POST['remove'])){
          $RMSong = "DELETE FROM song WHERE songid='" . addslashes($_POST['songid']) . "' ";
          $RMLang = "DELETE FROM language WHERE songid='" . addslashes($_POST['songid']) . "' ";

          if(mysql_query($RMLang)){
            if(!mysql_query($RMSong)){
              echo "<table width=\"100%\" style=\"background-color:red; color:white;\"><tr><td>Error ".mysql_errno()."<br /> Removal Failed, Server Responded: " . mysql_error() ." </td></tr></table>";
            }
          }
          else
          {
             echo "<table width=\"100%\" style=\"background-color:red; color:white;\"><tr><td>Error ".mysql_errno()."<br /> Removal Failed, Server Responded: " . mysql_error() ." </td></tr></table>";
          }
        }
		else if(isset($_POST['edescription'])){
			$UPEPI = "UPDATE EPISODE SET date='".addslashes($_POST['edate'])."', starttime='".addslashes($_POST['etime'])."',description='".addslashes($_POST['edescription'])."' WHERE callsign='" . $CALLSHOW . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "'";
			$UPLAN = "update language set starttime='" . addslashes($_POST['etime']) . "' where programname='". addslashes($_POST['program']). "' and callsign='" . $CALLSHOW ."' and date='".addslashes($_POST['user_date'])."' and starttime='" . addslashes($_POST['user_time']) . "' ";
			if(!mysql_query($UPEPI)){
				echo "ERROR". mysql_error();
			}
			else{
				
				//echo $UPLAN;
				if(!mysql_query($UPLAN)){
					echo "ERROR #". mysql_errno() . "<br />" . mysql_error();
				}
			}
			
			//Needed as the primary information would be already contained within the previous query
			$PROGRAMQUERY = "select * from episode where callsign='". $CALLSHOW . "' and programname='" . addslashes($_POST['program']) . "' and date='". addslashes($_POST['edate']) . "' and starttime='" . addslashes($_POST['etime'])."'";
			$PROGRAMDATA = mysql_query($PROGRAMQUERY);
        	$PROGRAMARRAY = mysql_fetch_array($PROGRAMDATA);		
		}
        else{
          if(isset($_POST['songid'])){
            $SONGUP = "UPDATE SONG SET ";
            //begin checks
            if($_POST['time'] != ""){
              $SONGUP .= "time=\"" . addslashes($_POST['time']) . "\" ";
            }
			else{
				$SONGUP .= " time=NULL ";
			}
            if(isset($_POST['category'])){
                   $SONGUP .= ", category=\"" . addslashes($_POST['category']) . "\" ";
            }
            if(isset($_POST['artist'])){
                   $SONGUP .= ", artist=\"" . addslashes($_POST['artist']) . "\" ";
            }
			if($_POST['minutes']!=""){
                   $SONGUP .= ", Spoken=\"" . addslashes($_POST['minutes']) . "\" ";
            }
            if(isset($_POST['title'])){
              if($_POST['title'] != ""){
                 $SONGUP .= ", title=\"" . addslashes($_POST['title']) . "\" ";
              }
            }
            if(isset($_POST['album'])){
                 $SONGUP .= ", album=\"" . addslashes($_POST['album']) . "\" ";
            }
            if(isset($_POST['playlistnumber'])){
              if($_POST['playlistnumber'] != ""){
                 $SONGUP .= ", playlistnumber=\"" . addslashes($_POST['playlistnumber']) . "\" ";
              }
              else{
                $SONGUP .= ", playlistnumber=null ";
              }
            }
            if(isset($_POST['cancon'])){
              $SONGUP .= ", cancon=\"1\" ";
            }
            else
            {
              $SONGUP .= ", cancon=\"0\" ";
            }
            if(isset($_POST['instrumental'])){
              $SONGUP .= ", instrumental=\"1\" ";
            }
            else
            {
              $SONGUP .= ", instrumental=\"0\" ";
            }
            if(isset($_POST['hit'])){
              $SONGUP .= ", hit=\"1\" ";
            }
            else
            {
              $SONGUP .= ", hit=\"0\" ";
            }
            //end checks
            $SONGUP .= " where songid=\"" . addslashes($_POST['songid']) . "\" ";
			$LANGUP = "UPDATE Language SET languageid=\"" . addslashes($_POST['languageid']) . "\" where songid=\"" . addslashes($_POST['songid']) . "\"";
            //save SQL Query, Execute in header (below Title)?
            if(!mysql_query($SONGUP,$con)){
              echo "<table width=\"100%\" style=\"background-color:red; color:white;\"><tr><td>Error " . mysql_errno() . ": " . mysql_error() . "</td></tr></table>";
            }
			else{
				if(!mysql_query($LANGUP,$con)){
					echo "<table width=\"100%\" style=\"background-color:red; color:white;\"><tr><td>Error " . mysql_errno() . ": " . mysql_error() . "</td></tr></table>";
				}
			}
            //announce Result
          }
		}
		
		$SQLProg = "SELECT Genre.*, Program.length from Genre, Program where Program.programname=\"" . addslashes($_POST['program']) . "\" and program.callsign=\"" . addslashes($CALLSHOW) . "\" and Program.genre=Genre.genreid";
		if(!($result = mysql_query($SQLProg))){
			echo mysql_error();
		}
		if(!($Requirements = mysql_fetch_array($result))){
			echo mysql_error();
		}
		$SQL2PR = "SELECT * from Program where programname=\"" . addslashes($_POST['program']) . "\" and callsign=\"" . addslashes($CALLSHOW) . "\" ";
		if(!($result2 = mysql_query($SQL2PR))){
			echo mysql_error();
		}
		if(!($Req2 = mysql_fetch_array($result2))){
			echo mysql_error();
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
		$SQLCOUNTCC = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and cancon='1' ";
		$resultCC = mysql_query($SQLCOUNTCC);
		$RECCC = mysql_num_rows($resultCC);
		$SQLCOUNTPL = "Select songid from SONG where callsign='" . addslashes($CALLSHOW) . "' and programname='" . addslashes($_POST['program']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and playlistnumber IS NOT NULL";
		$resultPL = mysql_query($SQLCOUNTPL);
		$RECPL = mysql_num_rows($resultPL);
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>DPL Editor</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
        <table border="0" align="center" width="1354">
        <tr><td width="1350" colspan="4">
           <img src="/images/Ckxu_logo_PNG.png" alt="ckxu login"/>
        </td></tr>
        <tr><td width="1350" colspan="2" style="background-color:white;">
	<h2>Update Program Log</h2>
	<?php
	echo "</td><td width=\"200px\"  style=\"background-color:white;\">";
		echo "Show Classification:  <strong>" . $CLA . "</strong>";
	echo "</td><td width=\"200px\""; 
	if($RECCC>=$CC){
	 	echo "style=\"background-color:lightgreen;\">";
	}
	else{
		echo "style=\"background-color:yellow;\">";
	}
		echo "Canadain Content Required:  <strong>" . $RECCC . "/" . $CC . "</strong>";
	echo "</td><td width=\"150px\"";
	 
	if($RECPL>=$PL){
	 	echo "style=\"background-color:lightgreen;\">";
	}
	else{
		echo "style=\"background-color:yellow;\">";
	}
		echo "Playlist Required:  <strong>" . $RECPL. "/" . $PL . "</strong>";
	echo "</td></tr>";
             //echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
             $br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser they use.
            //echo $br;

            if(ereg("opera", $br)) {
              //echo 'Browser Supported';
            //    header("location: originalhomepage.php");
            }
            else if(ereg("chrome", $br)) {
              echo "<tr><td colspan=\"100%\>
              <h3 width=\"100%\" style=\"background-color:yellow; color:black;\"><strong>WARNING: This browser does not support the needed HTML5 forms on Windows<br />
              please launch or download opera that supports these required forms. This does not apply to MAC OS</strong></h3>
              </td></tr>";
            //    header("location: originalhomepage.php");
            }
            else if(ereg("safari", $br)) {
              echo "<tr><td colspan=\"100%\>
              <h3 width=\"100%\" style=\"background-color:yellow; color:black;\"><strong>WARNING: This browser does not support the needed HTML5 forms on Windows<br />
              please launch or download opera that supports these required forms. This does not apply to MAC OS</strong></h3>
              </td></tr>";
            //    header("location: originalhomepage.php");
            }
            else {
              echo "<tr><td colspan=\"100%\>
              <h3 width=\"100%\" style=\"background-color:red; color:white;\"><strong>WARNING: This browser does not support the needed HTML5 forms
              please launch or download opera that supports these required forms</strong></h3>
              </td></tr>";
              //  header("location: alteredhomepage.php");
            }
        ?>
        <table width="1350" style="background-color:white;">
        <tr><th width="8%">
        Air Date
        </th><th width="6%">
        Air Time
        </th><th width="14%">
        Program
        </th><th width="7%">
        Station
        </th><th width="58%">
        Description
        </th><th width="8%">
        Pre-Record
        </th><th width="5%">

        </th>
        </tr>
        <tr><form name="episode" action="/Episode/p3update.php" method="POST">
        	<td valign="top">
	    <?php 
	    	//Air Date
	    	//echo $PROGRAMARRAY['date'];
			echo "<input type=\"text\" name=\"user_date\" hidden value=\"".$PROGRAMARRAY['date']."\"/>" ;
			echo "<input type=\"date\" name=\"edate\" value=\"".$PROGRAMARRAY['date']."\"/>" ;
		
		 ?>
        </td><td valign="top">
	    <?php 
	    	//Air Time
	    	//echo $PROGRAMARRAY['starttime'];
			echo "<input type=\"text\" name=\"user_time\" hidden value=\"".$PROGRAMARRAY['starttime']."\"/>" ;
			echo "<input type=\"time\" name=\"etime\" value=\"".$PROGRAMARRAY['starttime']."\"/>" ;
		
		 ?>
        </td><td valign="top">
             <?php 
             echo "<input type=\"text\" name=\"program\" hidden value=\"".$PROGRAMARRAY['programname']."\"/>" ;
             echo $PROGRAMARRAY['programname'];
             ?>
        </td><td valign="top">
             <?php 
             echo "<input type=\"text\" name=\"callsign\" hidden value=\"".$PROGRAMARRAY['callsign']."\"/>" ;
             echo $PROGRAMARRAY['callsign'];
             ?>
        </td><td valign="top">
             <?php 
	    	//Description
			echo "<input type=\"text\" size=\"100\" name=\"edescription\" value=\"".$PROGRAMARRAY['description']."\"/>" ;
		
		 ?>
        </td><td valign="top">
	    <?php echo $PROGRAMARRAY['prerecorddate']; ?>
        </td></tr>
        <tr><td colspan="100%"><input type="submit" value="Update Episode Title" /></td></tr></form>


        
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

        <tr>
            <table colspan="7" width="1350" valign="top" style="background-color:white;">
                  <tr><!-- Header Definitions for songs -->
                       <th width="5%" colspan="2">
                           Type
                       </th>
                       <th>
                           Playlist
                       </th>
                       <th>
                           Spoken
                       </th>
                       <th>
                           Time
                       </th>
                       <th>
                           Title
                       </th>
                       <th>
                           Artist
                       </th>
                       <th>
                           Album (Release Title)
                       </th>
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

                       </th>
              </tr>
               <tr> <!-- Row for displaying already entered data -->
                   <?php
                     $query = "select * from SONG where callsign='" . addslashes($PROGRAMARRAY['callsign']) . "' and programname='" . addslashes($PROGRAMARRAY['programname']) . "' and date='" . addslashes($PROGRAMARRAY['date']) . "' and starttime='" . addslashes($PROGRAMARRAY['starttime']) . "' order by time desc, songid";
                     $listed=mysql_query($query,$con);
                     if(mysql_num_rows($listed)=="0"){
                       echo "<tr><td colspan=\"11\" style=\"background-color:yellow;\">no data returned</td></tr>";
                     }
                     else
                     {
                         while ($list=mysql_fetch_array($listed))
                         {
                           echo "<tr><form action=\"p3update.php\" method=\"POST\" name=\"update\" >";
                                //$_POST['callsign']."' and programname='" . $_POST['program'] . "' and date='". $_POST['user_date']."' and starttime='".$_POST['user_time']
                                echo "<input type=\"text\" name=\"program\" hidden=\"true\" value=\"" . $PROGRAMARRAY['programname'] . "\">";
                                echo "<input type=\"text\" name=\"callsign\" hidden=\"true\" value=\"" . $PROGRAMARRAY['callsign'] . "\">";
                                echo "<input type=\"text\" name=\"user_date\" hidden=\"true\" value=\"" . $PROGRAMARRAY['date'] . "\">";
                                echo "<input type=\"text\" name=\"user_time\" hidden=\"true\" value=\"" . $PROGRAMARRAY['starttime'] . "\">";
                                echo "<input type=\"text\" name=\"songid\" hidden=\"true\" value=\"" . $list['songid'] . "\">";
                           echo "<td colspan=\"2\">";
                                //echo "<input type=\"text\" name=\"category\" value=\"" . $list['category'] . "\" size=\"10\">";
                                $OPT = "<select name=\"category\">";
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
								if($list['category']=="53")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">53, Sponsored Promotion</option>";
								
								$OPT .= "<OPTION value=\"52\"";
								if($list['category']=="52")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">52, Sponsor Indentification</OPTION>";
								
								$OPT .= "<OPTION value=\"51\"";
								if($list['category']=="51")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">51, Commercial</option>";
								
								$OPT .= "<OPTION value=\"45\"";
								if($list['category']=="45")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= "> 45, Show Promo</option>";
								
								$OPT .= "<OPTION value=\"44\"";
								if($list['category']=="44")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">44, Programmer/Show ID</option>";
								
								
								$OPT .= "<OPTION value=\"43\"";
								if($list['category']=="43")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">43, Station ID</option>";
								
								$OPT .= "<OPTION value=\"42\"";
								if($list['category']=="42")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">42, Tech Test</option>";
								
								$OPT .= "<OPTION value=\"41\"";
								if($list['category']=="41")
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
								if($list['category']=="36")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">36, Experimental</option>";
								
                                $OPT .= "<option value=\"35\"";
								if($list['category']=="35")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">35, NonClassical Religious</option>";
								
                                $OPT .= "<option value=\"34\"";
								if($list['category']=="34")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">34, Jazz and Blues</option>";
								
                                $OPT .= "<option value=\"33\"";
								if($list['category']=="33")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">33, World/International</option>";
								
                                $OPT .= "<option value=\"32\"";
								if($list['category']=="32")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">32, Folk</option>";
								
                                $OPT .= "<option value=\"31\"";
								if($list['category']=="31")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">31, Concert</option>";
								
								// CATEGORY 2 ---------------------------------------
								if($list['category']=="3"){
									$OPT .= "<OPTION value=\"3\" selected=\"true\" >3, Special Interest</option>";
								}
								
								$OPT .= "<OPTION value=\"24\"";
								if($list['category']=="24")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">24, Easy Listening</option>";
								
								$OPT .= "<OPTION value=\"23\"";
								if($list['category']=="23")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">23, Acoustic</option>";
								
								$OPT .= "<OPTION value=\"22\"";
								if($list['category']=="22")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">22, Country</option>";
								
								$OPT .= "<OPTION value=\"21\"";
								if($list['category']=="21")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">21, Pop, Rock and Dance</option>";
								
								if($list['category']=="2"){
									$OPT .= "<OPTION value=\"2\" selected=\"true\" >2, Popular Music</option>";
								}
								
								$OPT .= "<OPTION value=\"12\"";
								if($list['category']=="12")
								{
									$OPT .= "selected=\"true\" ";
								}
								$OPT .= ">12, PSA/Spoken Word Other</option>";
								
								$OPT .= "<OPTION value=\"11\"";
								if($list['category']=="11")
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
                                echo $OPT;

                           echo "</td><td>";
                                echo "<input type=\"number\"  min=\"0\" name=\"playlistnumber\" size=\"5\" value=\"" . $list['playlistnumber'] . "\">";
                           echo "</td><td>";
                                echo "<input type=\"number\" ";
								if($list['category']!="12" || $list['category']!="11"){
									//echo " disabled='true' ";
								}
								else{
								}
                                echo " name=\"minutes\" size=\"5\" value=\"" . $list['Spoken'] . "\">";
                           echo "</td><td>";
                                //echo $list['time'];
                                echo "<input type=\"time\" name=\"time\" size=\"8\" value=\"" . $list['time'] . "\">";
                           echo "</td><td>";
                                //echo $list['title'];
                                echo "<input type=\"text\" name=\"title\" size=\"20\" value=\"" . $list['title'] . "\">";
                           echo "</td><td>";
                                //echo $list['artist'];
                                echo "<input type=\"text\" name=\"artist\" size=\"20\" value=\"" . $list['artist'] . "\">";
                           echo "</td><td>";
                                //echo $list['album'];
                                echo "<input type=\"text\" name=\"album\" size=\"20\" value=\"" . $list['album'] . "\">";
                           echo "</td><td>";
						  		echo "<input type=\"text\" name=\"composer\" size=\"20\" value=\"" . $list['composer'] . "\">";
                           echo "</td><td>";
                                //echo $list['cancon'];
                                $CC = "<input type=\"checkbox\" name=\"cancon\" ";
                                if($list['cancon']=="1"){
                                  $CC.="checked=\"true\" >";
                                }
                                else{
                                  $CC.=" >";
                                }
                                echo $CC;
                           echo "</td><td>";
                                $HIT = "<input type=\"checkbox\" name=\"hit\" ";
                                if($list['hit']=="1"){
                                  $HIT.="checked=\"true\" >";
                                }
                                else{
                                  $HIT.=" >";
                                }
                                echo $HIT;
                           echo "</td><td>";
                                $INS = "<input type=\"checkbox\" name=\"instrumental\" ";
                                if($list['instrumental']=="1"){
                                  $INS.="checked=\"true\" >";
                                }
                                else{
                                  $INS.=" >";
                                }
                                echo $INS;
                           $songlang = mysql_query("select languageid from LANGUAGE where callsign=\"" . addslashes($list['callsign']) . "\" and programname=\"" . addslashes($list['programname']) . "\" and date=\"" . addslashes($PROGRAMARRAY['date']) . "\" and starttime=\"" . addslashes($PROGRAMARRAY['starttime']) . "\" and songid=\"". addslashes($list['songid']) ."\"");
                           $rowlang = mysql_fetch_array($songlang);
                           echo "</td><td>";
                                //echo $rowlang['languageid'];
                                echo "<input type=\"text\" name=\"languageid\" size=\"8\" value=\"" . $rowlang['languageid'] . "\">";
                           echo "</td><td>";
                                echo "<input type=\"submit\" value=\"Update\">";
                           echo "</td></form>";
                           echo "<td><form name=\"remove\" action=\"/Episode/p3update.php\" method=\"POST\">";
                                echo "<input type=\"text\" name=\"program\" hidden=\"true\" value=\"" . $PROGRAMARRAY['programname'] . "\">";
                                echo "<input type=\"text\" name=\"callsign\" hidden=\"true\" value=\"" . $PROGRAMARRAY['callsign'] . "\">";
                                echo "<input type=\"text\" name=\"user_date\" hidden=\"true\" value=\"" . $PROGRAMARRAY['date'] . "\">";
                                echo "<input type=\"text\" name=\"user_time\" hidden=\"true\" value=\"" . $PROGRAMARRAY['starttime'] . "\">";
                                echo "<input type=\"text\" name=\"songid\" hidden=\"true\" value=\"" . $list['songid'] . "\">";
                                echo "<input type=\"text\" name=\"remove\" hidden=\"true\" value=\"1\">";
                           echo "<input type=\"submit\" value=\"Remove\">";
                           echo "</td></form>";
                           echo "</tr>
                           ";
                         }
                     }

                   ?>
               </tr>
        </tr>
        <tr>
        <th colspan="5">
        </th>
        <th colspan="1">
        Total Spoken Time
        </th>
        <th colspan="1">
        Time Complete
        </th>
        </tr>
        <tr>
        <td>
        <?php
             if($_SESSION['usr']!="user"){
               echo /*"<form name=\"logout\" action=\"/logout.php\" method=\"POST\">
              <input type=\"submit\" value=\"Logout\">
              </form>
              </td><td>*/
              "<form name=\"exit\" action=\"/masterpage.php\" method=\"POST\">
              <input type=\"submit\" value=\"Menu\">
              </form>";
             }
             else{
               echo "<form name=\"exit\" action=\"/djhome.php\" method=\"POST\">
              <input type=\"submit\" value=\"Logout\">
              </form>";
             }
        ?>
        </td>
        <td>
        <?php 
             if($_SESSION['usr']!="user"){
               echo "<form name=\"Edit\" action=\"/Episode/p1update.php\" method=\"POST\">
                    <input type=\"submit\" value=\"Search\">
                </form>";
             }
        ?>
        </td>
        <td>
        <form name="refresh" action="/episode/p3update.php" method="POST">
        <input type="text" hidden="true" name="callsign" value=<?php echo "\"" . $PROGRAMARRAY['callsign'] . "\"" ?> />
            <input type="text" hidden="true" name="program" value=<?php echo "\"" . $PROGRAMARRAY['programname'] . "\"" ?> />
            <input type="text" hidden="true" name="user_date" value=<?php echo "\"" . $PROGRAMARRAY['date'] . "\"" ?> />
            <input type="text" hidden="true" name="user_time" value=<?php echo "\"" . $PROGRAMARRAY['starttime'] . "\"" ?> />
            <input type="submit"value="Refresh">
        </form>
        </td>
        <td>
        <form name="append" action="/episode/p2insertEP.php" method="POST">
        <input type="text" hidden="true" name="callsign" value=<?php echo "\"" . $PROGRAMARRAY['callsign'] . "\"" ?> />
            <input type="text" hidden="true" name="program" value=<?php echo "\"" . $PROGRAMARRAY['programname'] . "\"" ?> />
            <input type="text" hidden="true" name="user_date" value=<?php echo "\"" . $PROGRAMARRAY['date'] . "\"" ?> />
            <input type="text" hidden="true" name="user_time" value=<?php echo "\"" . $PROGRAMARRAY['starttime'] . "\"" ?> />
            <input type="submit" value="Add to Log">
        </form>
        </td>

        <td colspan="1">
        <?php echo $PROGRAMARRAY['totalspokentime'];?>
        </td>
        <td colspan="1">
        <?php echo $PROGRAMARRAY['endtime']; ?>
        </td>
        <td colspan="1">
        </td>
        </tr>
        <tr><td colspan="100%" height="20">
        <hr />
        </td></tr>
        <tr><td colspan="12">

        </td><td>
        <img src="/images/mysqls.png" alt="MySQL Powered" />
        </td></tr>

        </table>
</body>
</html>