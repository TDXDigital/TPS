<?php
date_default_timezone_set("UTC");
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN/functions.php";
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN/db_connect.php";
sec_session_start();
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>TPS Broadcast</title>
</head>
<html>
<body>
      <div class="topbar">
           <a class="right" href="../../logout.php"> Logout </a>Welcome, <?php echo(strtoupper(
              filter_input(INPUT_SERVER, 'fname'))); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
        <h2>Update Program Log</h2>
        <table align="left" border="0" height="100">
        <tr>
        <th>Station</th>
        <th width="250">Program Name</th>
        <th width="100">Date</th>
        <th width="100">PreRecord</th>
        <th width="100">Air Time</th>
        <th width="250">Decription</th>
        <th>Edit</th>
        </tr>
        <?php
        $date = filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING)?:"%";
        $pr = filter_input(INPUT_POST, "prerecord", FILTER_SANITIZE_STRING)?:"%";
        $episodes = array();
        if($stmt = $mysqli->prepare(
            "select programname, date, prerecorddate, starttime, EpNum, callsign, description from `episode`".
            "where date like ?  and programname like ?")) {
            $stmt->bind_param("ss", $date, $pr);
            $stmt->execute();
            $stmt->bind_result($programName, $date, $preRecordDate, $startTime, $epNum, $callsign, $description);
            while($stmt->fetch()) {
                $episodes[$epNum] = [$callsign, $programName, $date, $preRecordDate, $startTime, $description];
            }
            $stmt->close();
        }
        foreach ($episodes as $epNum=>$row){
                echo '<form name="view" action="EPV2/p3update.php" method="POST">
                <tr class="striped"><td>';
                //column 1
                   echo $row[0];
                   echo '<input name="callsign" hidden value="'.$row[0].'" />';

               echo '</td><td>';
               //column 2
                   echo $row[1];
                   echo '<input name="program" hidden value="'.$row[1].'" />';

               echo '</td><td>';
               //column 3
                   echo $row[2];
                    echo '<input name="user_date" hidden value="'.$row[2].'" />';

               echo '</td><td>';
                    //column 4
                   echo $row[3];

               echo '</td><td>';
               //column 5
                   echo $row[4];
                   echo '<input name="user_time" hidden="true" value="' . $row[4] . '" />';
                echo '</td><td>';
                //column 6
                echo $row[5];

               echo '</td><td>';
               echo "<input type=\"submit\" value=\" Edit \" />";

               echo'</td></tr></form>';
         }
        echo '</table>';
echo '</tr><tr height="40" valign="bottom"><td colspan="2" style="text-align: center;"><hr/><a href="../">
Return to main Admin Page</a></td></tr>';
?>
        </td><tr>
<td height="10" style="text-align:left">
<img src="../../images/mysqls.png" alt="MySQL"></td></span>
</td></tr>
</table>
</body>
</html>
