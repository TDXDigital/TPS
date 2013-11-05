<?php
      session_start();

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT callsign, stationname from STATION order by callsign";
        $result=mysql_query($sql);

        $options="";//<OPTION VALUE=0>Choose</option>";
        while ($row=mysql_fetch_array($result)) {
            $name=$row["stationname"];
            $callsign=$row["callsign"];
            $options.="<OPTION VALUE=\"$callsign\">".$name."</option>";
        }

}
else{
	echo 'ERROR!';
}
?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>Station Insertion</title>
</head>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
           <table width="1000">
           <tr><td colspan="3">
           <img src="/images/Ckxu_logo_PNG.png" alt="ckxu login"/>
           </td></tr></table><table width="1000" style="background-color:white;">
	<tr><td colspan="100%"><h2>View Station</h2></td></tr>

        <tr><th colspan="100%">
        Direct Selection
        </th></tr>
        <tr><td colspan="2">
        <form action="/station/p2viewstation.php" name="callsign" method="POST">
        <select name="callsign">
        <?php echo $options;?>
        </select>
        </td><td width="100%">
        <input type="submit" value="View" />
        </form>
        </td></tr>
	<tr><td colspan="100%" height="20">
        <hr/>
        </td></tr>
        <tr>
        <td>
        <form name="logout" action="/logout.php" method="POST">
              <input type="submit" value="Logout">
        </form>
        </td>
        <td>
        <form name="return" action="/masterpage.php" method="POST">
              <input type="submit" value="Return">
        </form>
        </td>
        <td></td>
        <td style="text-align:right;" width="100%">
        <img src="/images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>
        </table>
</body>
</html>
