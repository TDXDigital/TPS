<?php
date_default_timezone_set("UTC");

include_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."../TPSBIN/functions.php";
include_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."../TPSBIN/db_connect.php";
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>TPS Broadcast</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
           </div>

      <table border="0" align="center" class="striped">
      <tr>
           <td align="center"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td>
<?php
        $callsql="SELECT callsign, stationname from `station` order by callsign";
        $callresult=$mysqli->query($callsql);

        $calloptions="<option value=%>Any Station</option>";
        while ($row=$callresult->fetch_array(MYSQLI_ASSOC)) {
            $name=$row["stationname"];
            $callsign=$row["callsign"];
            $calloptions.="<OPTION VALUE=\"$callsign\">".$name."</option>";
        }

        $djsql="SELECT * from `dj` order by djname";
        $djresult=$mysqli->query($djsql);

        $djoptions="<option value=\"%\">Any Host</option>";//<OPTION VALUE=0>Choose</option>";
        while ($djrow=$djresult->fetch_array(MYSQLI_ASSOC)) {
            $Alias=$djrow["Alias"];
            $name=$djrow["djname"];
            $djoptions.="<OPTION VALUE=\"$Alias\">" . $name . "</option>";
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="100%">
        <h2>Update Program Log</h2>
        </td></tr>
        <tr><th colspan="2" width="33%">
        Program Name [% is wildcard]
        </th><th width="33%">
        Station Callsign
        </th><th width="33%">
        Air Date
        </th><th width="33%">
        PreRecord Date
        </th><!--<th width="33%">
        Description
        </th>-->
        </tr>
             <form name="selections" action="p2update.php" method="post">
             <tr>
             <td colspan="2">
                 <input name="name" type="text" size="33%" value="%"/>
             </td>
             <td>
                 <select name="callsign">
                         <?php echo $calloptions;?>
                 </select>
             </td>
             <td>
                 <input name="date" type="date" value="<?php
                 echo date("Y-m-d");
                 ?>" size="33%"/>
             </td>
             <td>
                 <input name="prerecord" type="date" size="33%"/>
             </td>
            <td>
                <input type="submit" value="Submit" />
                </form>
            </td>
        </tr>
</tr><tr height="40" valign="bottom"><td colspan="100%" style="text-align:bottom;"><hr/></td></tr>
        <tr>
        <td>
        <form name="logout" action="../../logout.php" method="POST">
              <input type="submit" value="Logout">
        </form>
        </td>
        <td>
        <form name="return" action="../../masterpage.php" method="POST">
              <input type="submit" value="Return">
        </form>
        </td>
        <td colspan="2"></td>
        <td style="text-align:right;">
        <img src="../../images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>

        </table>
        </td>
        </tr>
        </table>
</body>
</html>
