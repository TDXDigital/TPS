<?php
      session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login.php');}
}
else{
	echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
}

        $INSENDQU = "update episode set endtime='" . addslashes($_POST['end']) . "', totalspokentime='". addslashes($_POST['spoken'])."' WHERE callsign='".addslashes($_POST['callsign'])."' and programname='".addslashes($_POST['program'])."' and date='".addslashes($_POST['user_date'])."' ";

        mysql_query($INSENDQU,$con);
        $PROGRAMQUERY = "select * from episode where callsign='". addslashes($_POST['callsign'])."' and programname='" . addslashes($_POST['program']) . "' and date='". addslashes($_POST['user_date'])."' and starttime='".addslashes($_POST['user_time'])."'";
        $PROGRAMDATA = mysql_query($PROGRAMQUERY);
        $PROGRAMARRAY = mysql_fetch_array($PROGRAMDATA);
        if( mysql_num_rows($PROGRAMDATA) == "0" )
        {
          echo 'No Shows Match Given Data, or No Data Provided <br />';
        }
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>DPL Episode</title>
</head>
<html>
<body>
      <div class="topbar">
           <a class="right" href="..//logout.php"> Logout </a>Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
        <table border="0" align="center" width="1354">
        <tr><td width="1350" colspan="4">
           <img src="..//images/Ckxu_logo_PNG.png" alt="ckxu login"/>
        </td></tr>
        <tr><td width="1350" colspan="2" style="background-color:white;">
	<h2>View Program Log</h2>
	</td></tr>
        <?php
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
              echo "<tr><td>
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
        <tr><td valign="top">
	    <?php echo $PROGRAMARRAY['date']; ?>
        </td><td valign="top">
	    <?php echo $PROGRAMARRAY['starttime']; ?>
        </td><td valign="top">
             <?php echo $PROGRAMARRAY['programname'];?>
        </td><td valign="top">
             <?php echo $PROGRAMARRAY['callsign'];?>
        </td><td valign="top">
             <?php echo $PROGRAMARRAY['description']; ?>
        </td><td valign="top">
	    <?php echo $PROGRAMARRAY['prerecorddate']; ?>
        </td></tr>


        
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
                       <th width="5%">
                           Type
                       </th>
                       <th width="5%">
                           Playlist
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
                     $query = "select * from SONG where callsign='" . addslashes($PROGRAMARRAY['callsign']). "' and programname='" . addslashes($PROGRAMARRAY['programname']) . "' and date='" . addslashes($PROGRAMARRAY['date']) . "' and starttime='" . addslashes($PROGRAMARRAY['starttime']) . "' order by time";
                     $listed=mysql_query($query,$con);
                     if(mysql_num_rows($listed)=="0"){
                       echo "<tr><td colspan=\"11\" style=\"background-color:yellow;\">no data returned</td></tr>";
                     }
                     else
                     {
                         while ($list=mysql_fetch_array($listed))
                         {
                           echo "<tr>";
                           echo "<td>";
                                echo $list['category'];
                           echo "</td><td>";
                                echo $list['playlistnumber'];
                           echo "</td><td>";
                                echo $list['time'];
                           echo "</td><td>";
                                echo $list['title'];
                           echo "</td><td>";
                                echo $list['artist'];
                           echo "</td><td>";
                                echo $list['album'];
                           echo "</td><td>";
                                echo $list['cancon'];
                           echo "</td><td>";
                                echo $list['hit'];
                           echo "</td><td>";
                                echo $list['instrumental'];
                           $songlang = mysql_query("select languageid from LANGUAGE where callsign='" . addslashes($list['callsign']) . "' and programname='" . addslashes($list['programname']) . "' and date='" . addslashes($_POST['user_date']) . "' and starttime='" . addslashes($_POST['user_time']) . "' and songid='". addslashes($list['songid']) ."'");
                           $rowlang = mysql_fetch_array($songlang);
                           echo "</td><td>";
                                echo $rowlang['languageid'];
                           echo "</td>";
                           echo "</tr>";
                         }
                     }

                   ?>
               </tr>
        </tr>
        <tr>
        <th colspan="4">
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
          if($_SESSION['usr']=='user')
          {
            echo "<form name=\"exit\" action=\"p1insertEP.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"New Log\">";
            echo "</form></td>";
            echo "<td><form name=\"exit\" action=\"VERLogout.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Logout\">";
            echo "</form>";

          }
          else
          {
            echo "<form name=\"exit\" action=\"../masterpage.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Exit\">";
            echo "</form><td></td>";
          }
        ?>

        </td>
        <td>
        <form name="edit" action="EPV2/p3update.php" method="POST">
            <input type="text" hidden="true" name="callsign" value=<?php echo "\"" . $PROGRAMARRAY['callsign'] . "\"" ?> />
            <input type="text" hidden="true" name="program" value=<?php echo "\"" . $_POST['program'] . "\"" ?> />
            <input type="text" hidden="true" name="user_date" value=<?php echo "\"" . $_POST['user_date'] . "\"" ?> />
            <input type="text" hidden="true" name="user_time" value=<?php echo "\"" . $_POST['user_time'] . "\"" ?> />
            <input type="submit" value="Edit Log">
        </form>
        </td>
        <td>
        <form name="append" action="p2insertEP.php" method="POST">
        <input type="text" hidden="true" name="callsign" value=<?php echo "\"" . $PROGRAMARRAY['callsign'] . "\"" ?> />
            <input type="text" hidden="true" name="program" value=<?php echo "\"" . $_POST['program'] . "\"" ?> />
            <input type="text" hidden="true" name="user_date" value=<?php echo "\"" . $_POST['user_date'] . "\"" ?> />
            <input type="text" hidden="true" name="user_time" value=<?php echo "\"" . $_POST['user_time'] . "\"" ?> />
            <input type="submit" value="Append Log">
        </form>
        </td>

        <td colspan="1">
        <?php 
              echo $PROGRAMARRAY['totalspokentime'];
        ?>
        </td>
        <td colspan="1">
        <?php 
              echo $PROGRAMARRAY['endtime']; 
        ?>
        </td>
        <td colspan="1">
        </td>
        </tr>
        <tr><td colspan="11" height="20">
        <hr />
        </td></tr>
        <tr><td colspan="10">

        </td><td>
        <img src="../images/mysqls.png" alt="MySQL Powered" />
        </td></tr>

        </table>
</body>
</html>