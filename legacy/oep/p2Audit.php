<?php session_start(); ?>
<head>
<link rel="stylesheet" type="text/css" href="../../../css/phpstyle.css" />
<title>TPS Broadcast Reports</title>
</head>
<html>
<body style="background-color:white;">
<?php

date_default_timezone_set("UTC");
include_once "../../TPSBIN/functions.php";
include_once "../../TPSBIN/db_connect.php";

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
        $AUDIT=$mysqli->query($BUFFSQL);
        while ($AUDITROW=$AUDIT->fetch_array(MYSQLI_ASSOC)) {
            // this will contain ALL of the setup to print a log
            // the end MUST have the <p class=break>
            $STAT = "SELECT * FROM STATION WHERE callsign='" . $AUDITROW['callsign'] . "'";
            $STQUE = $mysqli->query($STAT);
            $SROW = $STQUE->fetch_array(MYSQLI_ASSOC);
            echo "<table width=\"100%\" border=\"0\" >"; //style=\"background-color:black; color:white;\" >";
            // Row 1
            echo "<tr><td rowspan=\"3\" width=\"10%\">";
            echo "<img src=\"../" . $_SESSION['logo'] . "\" width=\"150px\">";//"CKXU PROGRAM LOG"; //image Here
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
            if (isset($AUDITROW['prerecorddate'])) {
                if ($AUDITROW['prerecorddate'] != "") {
                    echo "Pre-Record Date:" . $AUDITROW['prerecorddate'];
                } else {
                    $PR = "Not Pre-Recorded";
                }
            } else {
                $PR = "Not Pre-Recorded";
            }
            echo $PR;
            echo "</td><td>";
            // needs program info
            if (isset($AUDITROW['syndicatesource'])) {
                if ($AUDITROW['syndicatesource'] != "") {
                    echo "Syndicate source:" . $AUDITROW['syndicatesource'];
                } else {
                    echo "Not Syndicated";
                }
            } else {
                echo "Not Syndicated ";
            }
            echo "</td><td colspan=\"2\">";
            echo "Programmer(s): ";
            $GETDJ = "SELECT DJ.djname FROM DJ, PERFORMS, EPISODE WHERE PERFORMS.programname='" . addslashes($AUDITROW['programname']) . "' AND DJ.Alias=PERFORMS.Alias AND EPISODE.callsign='" . $AUDITROW['callsign'] . "' AND EPISODE.date='" . $AUDITROW['date'] . "'";
            $DJARRAY = $mysqli->query($GETDJ);
            $DJNAME = $DJARRAY->fetch_array(MYSQLI_ASSOC);
            echo $DJNAME['djname'];
            echo "</td></tr>";
            echo "</table>";
            echo "<table width=\"100%\" border=\"1\">";
            echo "<tr><th width=\"5%\">CAT</th><th width=\"5%\">Time</th><th width=\"20%\">Artist</th><th width=\"20%\">Title</th><th width=\"20%\">Release Title</th><th width=\"2%\">CC</th><th width=\"2%\">Hit</th><th width=\"2%\">Ins</th><th width=\"4%\">Language</th></tr>";
            $query = "SELECT * FROM SONG WHERE callsign='" . $AUDITROW['callsign'] . "' AND programname='" . addslashes($AUDITROW['programname']) . "' AND DATE='" . $AUDITROW['date'] . "' AND starttime='" . $AUDITROW['starttime'] . "' ORDER BY TIME, songid";
            $listed = $mysqli->query($query);
            if ($listed->num_rows == 0) {
                echo "<tr><td colspan=\"10\" style=\"background-color:yellow;\">no data returned</td></tr>";
            } else {
                while ($list = $listed->fetch_array(MYSQLI_ASSOC)) {
                    echo "<tr>";
                    echo "<td>";
                    echo $list['category'];
                    //echo "</td><td>";
                    //echo $list['playlistnumber'];
                    echo "</td><td>";
                    echo $list['time'];
                    echo "</td><td>";
                    if (isset($list['artist'])) {
                        echo $list['artist'];
                    }
                    /*else{
                      echo '<sub>Not Defined</sub>'; // For some reason this is only occuring on the first result...
                    } */
                    echo "</td><td>";
                    echo $list['title'];
                    echo "</td><td>";
                    if (isset($list['album'])) {
                        echo $list['album'];
                    }
                    echo "</td><td>";
                    echo $list['cancon'];
                    echo "</td><td>";
                    echo $list['hit'];
                    echo "</td><td>";
                    echo $list['instrumental'];
                    $songlang = $mysqli->query("SELECT languageid FROM LANGUAGE WHERE callsign='" . $list['callsign'] . "' AND programname='" . addslashes($list['programname']) . "' AND DATE='" . $AUDITROW['date'] . "' AND starttime='" . $AUDITROW['starttime'] . "' AND songid='" . $list['songid'] . "'");
                    $rowlang = $songlang->fetch_array(MYSQLI_ASSOC);
                    echo "</td><td>";
                    echo $rowlang['languageid'];
                    echo "</td>";
                    echo "</tr>";
                }
            }
            echo "</table></br>";
            echo '<p style="page-break-before: always;"> </p>';
        }
     echo "<table width=\"100%\" style=\"background-color:black; color:white\"><tr><td width=\"10%\" rowspan=\"2\"></td><td><h3>End Report</h3><br /></td></tr>";
     echo "<tr><td>   LEGEND</td><td> CC= Canadian Content, Ins = Instrumental, CAT = Category</td></tr></table>";
?>
</body>
</html>
