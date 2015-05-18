<?php session_start(); ?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/phpstyle.css" />
        <script type='text/javascript' src='https://www.google.com/jsapi'></script>
        <title>Audit Report</title>
    </head>
<body style="background-color:white;">
<?php

include('PHP/php-barcode.php');


$ShowStats = FALSE;
if(isset($_POST['sls'])){
	$ShowStats = TRUE;
}

if(isset($_POST['codef'])){
	$codef = addslashes($_POST['codef']);
}
else{
	$codef = "code39";//"codabar";
}

if($_POST['timef']=="12"){
	$time12 = TRUE;
}
else{
	$time12 = FALSE;
}
if($_POST['tms']=="1"){
	$Stime = TRUE;
}
else{
	$Stime = FALSE;
}
if(isset($_POST['ple'])){
	$ple = TRUE;
}
else{
	$ple = FALSE;
}

if($_POST['sort']=="En"){
	$sort = "EpNum";
}
else if($_POST['sort']=="St"){
	$sort = "starttime";
}
else{
	$sort = "programname";
}
if(isset($_POST['bcd'])){
	$barcode = TRUE;
}
else{
	$barcode = FALSE;
}


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
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
}
else{
  $BUFFSQL = "SELECT * FROM Episode where";
     $AND = "0";
        if(!mysql_select_db("CKXU")){
          header('Location: /login.php');
        }
        /*if($_POST['from']!=""){
          $BUFFSQL .= "date >='" . $_POST['from'] . "' ";
          $AND = "1";
        }
        if($_POST['to']!=""){
          if($AND=="0"){
            $BUFFSQL .= "date between '" . $_POST['to'] . "' ";
            $AND = "1";
          }
          else{
            $BUFFSQL .= " and date between '" . $_POST['to'] . "' ";
          }
          if($_POST['program']!=""){
              if($AND=="0"){
                $BUFFSQL .= " programname LIKE'" . $_POST['program'] . "' ";
              }
              else{
                $BUFFSQL .= " and programname LIKE'" . $_POST['program'] . "' ";
              }
          }
        }*/
        if($_POST['from']!=""){
          $BUFFSQL .= " date between '" . $_POST['from'] . "' and ";
          $AND = "1";
          if($_POST['to']!=""){
            $BUFFSQL .= "'" . $_POST['to'] . "'";

          }
          else{
            $BUFFSQL .= "'2100-01-01'";
          }
        }
        // echo $BUFFSQL; //DEBUG
        if($_POST['program']!=""){
              if($AND=="0"){
                $BUFFSQL .= " programname LIKE '%" . addslashes($_POST['program']) . "%' ";
                $AND = "1";
              }
              else{
                $BUFFSQL .= " and programname LIKE '%" . addslashes($_POST['program']) . "%' ";
              }
          }
		$BUFFSQL .= " order by ".$sort." asc";
        $AUDIT=mysql_query($BUFFSQL,$con);
        while ($AUDITROW=mysql_fetch_array($AUDIT)) {
          // this will contain ALL of the setup to print a log
          // the end MUST have the <p class=break>
          $STAT = "SELECT * from STATION where callsign LIKE '%" . $AUDITROW['callsign'] . "%'";
          $STQUE = mysql_query($STAT,$con);
          $SROW = mysql_fetch_array($STQUE);
		  
             echo "<table width=\"100%\" border=\"0\" style='font-size: inherit;' >"; //style=\"background-color:black; color:white;\" >";
                  // Row 1
             echo "<tr><td colspan=\"2\" >";
             echo "<img src=\"../../images/Ckxu_logo_PNG.png\" width=\"150px\">";//"CKXU PROGRAM LOG"; //image Here
             echo "</td><td colspan=\"3\">";
			 if($barcode){
			 	echo "<img src='PHP/createBarcode.php?bcd=".$AUDITROW['EpNum']."&type=".$codef."'/>";
			 	//include("");
			 }
			else{
				echo "Episode Number: ".$AUDITROW['EpNum'];
			}
             echo "</td></tr>
             <tr><td width=\"27%\" >";
             echo $SROW['frequency'] . " " . $SROW['website'];
             echo "</td><td  colspan=\"2\" width=\"37%\" >";
             echo $SROW['address'];
             echo "</td><td  width=\"15%\" >";
             echo "Booth Request Ph: <br />" . $SROW['boothphone'];
             echo "</td><td  width=\"20%\" >";
             echo "Program & Music Director Ph: <br />" . $SROW['directorphone'];
             echo "</td></tr>";
                  // Row 2
             echo "<tr><td>";
             echo "Show Name: " . $AUDITROW['programname'];
             echo "</td><td>";
             echo "Air Date: " . $AUDITROW['date'];
             echo "</td><td>";
             echo "Start Time:";
			 if($time12){
			 	echo to12hour($AUDITROW['starttime']);
			 }
             else{
                 echo $AUDITROW['starttime'];
             }
             echo "</td><td>";
             echo "End Time: ";
             if($time12){
             	echo to12hour($AUDITROW['endtime']);
			 }
             else{
                 echo $AUDITROW['endtime'];
             }
             echo "</td><td>";
             echo "Total Spoken Time: " . $AUDITROW['totalspokentime'];
             echo "</td></tr>";
                  // Row 3
             echo "<tr><td colspan=\"2\">";
             //$PR = "Pre-Record: ";
             if(isset($AUDITROW['prerecorddate']))
                  {
                    if($AUDITROW['prerecorddate'] != ""){
                        $PR = "Pre-Record Date:" . $AUDITROW['prerecorddate'];
                    }
                    else{
                      $PR = "Not Pre-Recorded";
                    }
                  }
                  else{
                    $PR = "Not Pre-Recorded";
                 }
             echo $PR;
             echo "</td><td>";
                  // needs program info
             if(isset($AUDITROW['syndicatesource']))
             {
               if($AUDITROW['syndicatesource'] != ""){
                 echo "Syndicate source:" . $AUDITROW['syndicatesource'];
               }
               else{
                 echo "Not Syndicated";
               }
             }
             else{
               echo "Not Syndicated ";
             }
             echo "</td><td colspan=\"2\">";
             echo "Programmer(s): ";
             /*$GETDJ = "SELECT DJ.djname from DJ, PERFORMS, EPISODE where PERFORMS.programname='".addslashes($AUDITROW['programname']).
             "' and DJ.Alias=PERFORMS.Alias and EPISODE.callsign='".$AUDITROW['callsign']."' and EPISODE.date='" . $AUDITROW['date'] .
             "'";*/
			 $GETDJ = "SELECT dj.djname from PERFORMS, DJ, EPISODE where episode.callsign = '" . addslashes($AUDITROW['callsign']) .
			 "' and performs.programname = '" . addslashes($AUDITROW['programname']) . "' and performs.Alias = dj.Alias group by dj.djname asc";
             $DJARRAY = mysql_query($GETDJ);
			 if(mysql_error()){
			 	echo mysql_error() . " - " . $GETDJ;
			 }
			 $looped = false;
             while($DJNAME = mysql_fetch_array($DJARRAY)){
				 if($looped==TRUE){
				 	echo ', ';
				 }
				 else{
				 	$looped = TRUE;
				 }
				 echo $DJNAME['djname'];
			 }
             echo "</td></tr>";
             echo "</table>";
             echo "<table width=\"100%\" border=\"1\" style='font-size: inherit; border-style:dotted solid; border-width: 1px;'>";
                  echo "<tr><th width=\"5%\" >Category</th>";
				  if($ple){
				  	echo "<th width=\"5%\">Playlist</th>";
				  }
				  if($Stime){
				  	echo "<th width=\"5%\">Time</th>";
				  }
                  echo "<th width=\"20%\">Artist</th><th width=\"20%\">Title</th><th width=\"20%\">Release Title</th><th>Composer</th><th width=\"2%\">CC</th><th width=\"2%\">Hit</th><th width=\"2%\">Ins</th><th width=\"4%\">Language</th></tr>";
				  if($_POST['type']=='MUO'){
                      $query = "select * from SONG where category between 20 and 40 and callsign='" . $AUDITROW['callsign']. "' and programname='" . addslashes($AUDITROW['programname']) . "' and date='" . $AUDITROW['date'] . "' and starttime='" . $AUDITROW['starttime'] . "' order by time, songid";
                  }
                  elseif($_POST['type']=='SPO'){
                      $query = "select * from SONG where category < 20 callsign='" . $AUDITROW['callsign']. "' and programname='" . addslashes($AUDITROW['programname']) . "' and date='" . $AUDITROW['date'] . "' and starttime='" . $AUDITROW['starttime'] . "' order by time, songid";
                  }
                  elseif($_POST['type']=='COM'){
                      $query = "select * from SONG where category > 50 callsign='" . $AUDITROW['callsign']. "' and programname='" . addslashes($AUDITROW['programname']) . "' and date='" . $AUDITROW['date'] . "' and starttime='" . $AUDITROW['starttime'] . "' order by time, songid";
                  }
                  elseif($_POST['type']=='ADM'){
                      $query = "select song.*,language.languageid from SONG left join language on language.songid=song.songid where song.callsign='" . $AUDITROW['callsign']. "' and song.programname='" . addslashes($AUDITROW['programname']) . "' and song.date='" . $AUDITROW['date'] . "' and song.starttime='" . $AUDITROW['starttime'] . "' order by time, songid";
                  }
                  else{
                      // Default to Complete
                      $query = "select song.*,language.languageid from SONG left join language on language.songid=song.songid where song.callsign='" . $AUDITROW['callsign']. "' and song.programname='" . addslashes($AUDITROW['programname']) . "' and song.date='" . $AUDITROW['date'] . "' and song.starttime='" . $AUDITROW['starttime'] . "' order by time, songid";
                  }
                  
                     $listed=mysql_query($query,$con);
                     if(mysql_errno($con)){
                         echo "<tr><td colspan=\"11\" style=\"background-color:red;\">Query Error: ".mysql_error()."</td></tr>";
                     }
                     if(mysql_num_rows($listed)=="0"){
                       echo "<tr><td colspan=\"100%\" style=\"background-color:yellow;\">no data returned</td></tr>";
                     }
                     /*elseif($_POST['type']=='ADM'){ // Processed Separate due to large data numbers
                         while ($list=mysql_fetch_array($listed)){
                             
                         }
                         
                     }*/
                     else
                     {
                         while ($list=mysql_fetch_array($listed))
                         {
                           if($list['category']=='51'){
                               echo "<tr><td style=\"background-color:#ffff99;\">";
                               //$promptsql="select * from promptlog where EpNum=`".$list['EpNum']."` and (SELECT ";
                               //echo $list.
                           }
                           echo "<tr>";
                            echo "<td>";
                                echo $list['category'];
							if($ple){
								echo "</td><td style='text-align: center;'>";
								if(isset($list['playlistnumber'])){
									echo $list['playlistnumber'];
								}
								else{
									echo "&nbsp;";	
								}
							}
                            if($Stime){
                           		echo "</td><td style='text-align: center;'>";
                                echo $list['time'];
						    }
                            echo "</td><td>";
                                if(isset($list['artist'])&&$list['artist']!=""){
                                    echo $list['artist'];
                                }
                                else{
                                    echo '&nbsp;'; // For some reason this is only occuring on the first result...
                                } 
                            echo "</td><td>";
                                echo $list['title'];
                            echo "</td><td>";
                                if(isset($list['album'])){
                                    echo $list['album'];
                                }
								else{
									echo "&nbsp;";	
								}
                            echo "</td><td>";
                                if(isset($list['composer'])){
                                    echo $list['composer'];
                                }
								else{
									echo "&nbsp;";	
								}
                            echo "</td><td style='text-align: center;'>";
                                if($list['cancon']=="1"){
                                	echo "X";
                                }
								else{
									echo "&nbsp;";	
								}
                            echo "</td><td style='text-align: center;'>";
                                if($list['hit']=='1'){
                                	echo "X";
                                }
								else{
									echo "&nbsp;";	
								}
                            echo "</td><td style='text-align: center;'>";
                                if($list['instrumental']){
                                	echo "X";
                                }
								else{
									echo "&nbsp;";	
								}
                                
                            //$songlang = mysql_query("select languageid from LANGUAGE where callsign='" . $list['callsign'] . "' and programname='" . addslashes($list['programname']) . "' and date='" . $AUDITROW['date'] . "' and starttime='" . $AUDITROW['starttime'] . "' and songid='". $list['songid'] ."'");
                            //$rowlang = mysql_fetch_array($songlang);
                            echo "</td><td>";
                                echo $list['languageid'];
                            echo "</td>";
                            echo "</tr>";
                         }
                     }
             echo "</table></br>";
			 /*if($ShowStats){
			 	echo "<div>";
				 echo "<div style='float:left;>";
				 	echo "4/5";
				 echo "</div>";
				echo "</div>";
			 }*/
             echo '<p style="page-break-before: always;"> </p>';
        }
}

             echo "<table width=\"100%\" style=\"background-color:black; color:white\"><tr><td width=\"10%\" rowspan=\"2\"></td><td><h3>End Report</h3><br /></td></tr>";
             echo "<tr><td>   LEGEND</td><td> CC= Canadian Content, Ins = Instrumental, CAT = Category</td></tr></table>";
        ?>
</body>
</html>