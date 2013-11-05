<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center"><img src="../images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td>
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $callsql="SELECT callsign, stationname from STATION order by callsign";
        $callresult=mysql_query($callsql,$con);

        $calloptions="<option value=%>Any Station</option>";
        while ($row=mysql_fetch_array($callresult)) {
            $name=$row["stationname"];
            $callsign=$row["callsign"];
            $calloptions.="<OPTION VALUE=\"$callsign\">".$name."</option>";
        }

        $djsql="SELECT * from DJ order by djname";
        $djresult=mysql_query($djsql,$con);

        $djoptions="<option value=\"%\">Any Host</option>";//<OPTION VALUE=0>Choose</option>";
        while ($djrow=mysql_fetch_array($djresult)) {
            $Alias=$djrow["Alias"];
            $name=$djrow["djname"];
            $djoptions.="<OPTION VALUE=\"$Alias\">" . $name . "</option>";
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="7">
        <h2>View Program</h2>
        </td></tr>

        <tr><th width="30%" colspan="2">
        Program Name [% is wildcard]
        </th><th width="20%">
        Station Callsign
        </th><th width="20%">
        Length (min)
        </th><th width="20%">
        Syndicate Source
        </th><th width="10%">
        Host
        </th>
        </tr>
             <form name="selections" action="p2view.php" method="post">
             <tr>
             <td colspan="2">
                 <input name="name" type="text" size="30" value="%"/>
             </td>
             <td>
                 <select name="callsign">
                         <?php echo $calloptions;?>
                 </select>
             </td>
             <td>
                 <input name="length" type="text" size="15" value="%"/>
             </td>
             <td>
                 <input name="syndicate" type="text" size="35" value="%"/>
             </td>
             <td>
                 <select name="dj1" disabled="true">
                         <?php echo $djoptions;?>
                 </select>
             </td>
             <td>
                <input type="submit" value="Search" />
                </form>
            </td>
        </tr>


        <?php

}
else{
	echo 'ERROR!';
}

echo '</tr><tr height="20"><td colspan="7" style="text-align:bottom;"><hr/></td></tr>';

?>
        <tr>
        <td>
        <form name="logout" action="../logout.php" method="POST">
              <input type="submit" value="Logout">
        </form>
        </td>
        <td>
        <form name="return" action="../masterpage.php" method="POST">
              <input type="submit" value="Return">
        </form>
        </td>
        <td colspan="3"></td>
        <td colspan="2" style="text-align:right;">
        <img src="../images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>
        
        </table>
        </td>
        </tr>
        </table>
</body>
</html>