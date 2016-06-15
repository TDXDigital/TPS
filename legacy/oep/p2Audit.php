<?php session_start(); ?>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body style="background-color:white;">
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
}
else{
  $BUFFSQL = "SELECT * FROM Episode where";
     $AND = "0";
        if(!mysql_select_db($_SESSION['DBNAME'])){
          header('Location: ../login.php');
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
        $AUDIT=mysql_query($BUFFSQL,$con);
        while ($AUDITROW=mysql_fetch_array($AUDIT)) {
          // this will contain ALL of the setup to print a log
          // the end MUST have the <p class=break>
          $STAT = "SELECT * from STATION where callsign='" . $AUDITROW['callsign'] . "'";
          $STQUE = mysql_query($STAT,$con);
          $SROW = mysql_fetch_array($STQUE);
             echo "<table width=\"100%\" border=\"0\" >"; //style=\"background-color:black; color:white;\" >";
                  // Row 1
             echo "<tr><td rowspan=\"3\" width=\"10%\">";
             echo "<img src=\"../".$_SESSION['logo']."\" width=\"150px\">";//"CKXU PROGRAM LOG"; //image Here
             echo "</td><td width=\"17%\" >";
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
             echo "Show Name: <br />" . $AUDITROW['programname'];
             echo "</td><td>";
             echo "Air Date: <br />" . $AUDITROW['date'];
             echo "</td><td>";
             echo "Start Time: <br />" . $AUDITROW['starttime'];
             echo "</td><td>";
             echo "End Time: <br />" . $AUDITROW['endtime'];
             echo "</td><td>";
             echo "Total Spoken Time: <br />" . $AUDITROW['totalspokentime'];
             echo "</td></tr>";
                  // Row 3
             echo "<tr><td colspan=\"2\">";
             $PR = "Pre-Record: ";
             if(isset($AUDITROW['prerecorddate']))
                  {
                    if($AUDITROW['prerecorddate'] != ""){
                        echo "Pre-Record Date:" . $AUDITROW['prerecorddate'];
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
             $GETDJ = "SELECT DJ.djname from DJ, PERFORMS, EPISODE where PERFORMS.programname='".addslashes($AUDITROW['programname'])."' and DJ.Alias=PERFORMS.Alias and EPISODE.callsign='".$AUDITROW['callsign']."' and EPISODE.date='" . $AUDITROW['date'] . "'";
             $DJARRAY = mysql_query($GETDJ);
             $DJNAME = mysql_fetch_array($DJARRAY);
             echo $DJNAME['djname'];
             echo "</td></tr>";
             echo "</table>";
             echo "<table width=\"100%\" border=\"1\">";
                  echo "<tr><th width=\"5%\">CAT</th><th width=\"5%\">Time</th><th width=\"20%\">Artist</th><th width=\"20%\">Title</th><th width=\"20%\">Release Title</th><th width=\"2%\">CC</th><th width=\"2%\">Hit</th><th width=\"2%\">Ins</th><th width=\"4%\">Language</th></tr>";
                  $query = "select * from SONG where callsign='" . $AUDITROW['callsign']. "' and programname='" . addslashes($AUDITROW['programname']) . "' and date='" . $AUDITROW['date'] . "' and starttime='" . $AUDITROW['starttime'] . "' order by time, songid";
                     $listed=mysql_query($query,$con);
                     if(mysql_num_rows($listed)=="0"){
                       echo "<tr><td colspan=\"10\" style=\"background-color:yellow;\">no data returned</td></tr>";
                     }
                     else
                     {
                         while ($list=mysql_fetch_array($listed))
                         {
                           echo "<tr>";
                           echo "<td>";
                                echo $list['category'];
                           //echo "</td><td>";
                                //echo $list['playlistnumber'];
                           echo "</td><td>";
                                echo $list['time'];
                           echo "</td><td>";
                                if(isset($list['artist'])){
                                  echo $list['artist'];
                                }
                                /*else{
                                  echo '<sub>Not Defined</sub>'; // For some reason this is only occuring on the first result...
                                } */
                           echo "</td><td>";
                                echo $list['title'];
                           echo "</td><td>";
                                if(isset($list['album'])){
                                  echo $list['album'];
                                }
                           echo "</td><td>";
                                echo $list['cancon'];
                           echo "</td><td>";
                                echo $list['hit'];
                           echo "</td><td>";
                                echo $list['instrumental'];
                           $songlang = mysql_query("select languageid from LANGUAGE where callsign='" . $list['callsign'] . "' and programname='" . addslashes($list['programname']) . "' and date='" . $AUDITROW['date'] . "' and starttime='" . $AUDITROW['starttime'] . "' and songid='". $list['songid'] ."'");
                           $rowlang = mysql_fetch_array($songlang);
                           echo "</td><td>";
                                echo $rowlang['languageid'];
                           echo "</td>";
                           echo "</tr>";
                         }
                     }
             echo "</table></br>";
             echo '<p style="page-break-before: always;"> </p>';
        }
}

             echo "<table width=\"100%\" style=\"background-color:black; color:white\"><tr><td width=\"10%\" rowspan=\"2\"></td><td><h3>End Report</h3><br /></td></tr>";
             echo "<tr><td>   LEGEND</td><td> CC= Canadian Content, Ins = Instrumental, CAT = Category</td></tr></table>";
        ?>
</body>
</html>
