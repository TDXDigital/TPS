<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<body>
      <div class="topbar">
           <a class="right" href="../logout.php"> Logout </a>Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
      <img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/>

<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
  $CALL = $_POST['callsign'];
  if($CALL == 'Choose')
  {
       header('Location: ../dj/p1updatestation.php');
  }
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT * from STATION where callsign='" . $CALL . "' order by callsign";
        $result=mysql_query($sql);

        while ($row=mysql_fetch_array($result)) {
            echo '<h1>' . $row['stationname'] . ' Information: <br/><br/><hr/></h1>
            <form name="st_update" action="../dj/p3updatestation.php" method="post" >';
            echo '<p>Callsign : <input name="callsign" type="text" size=4 value="' . $row['callsign'] . '"/><br/>';
            echo 'Description: <input name="name" type="text" size=45 value="' . $row['stationname'] . '"/><br />';
            echo 'Frequency : <input name="frequency" type="text" size=45 value="' . $row['frequency'] . '"/><br/>';
            echo 'Address : <input name="address" type="text" size=100 value="' . $row['address'] . '"/><br/>';
            echo 'Booth Phone : <input name="boothphone" type="text" size=20 value="' . $row['boothphone'] . '"/><br/>';
            echo 'Director Phone : <input name="directorphone" type="text" size=20 value="' . $row['directorphone'] . '"/><br/>';
            echo 'Station Designation : <input name="designation" type="text" size=45 value="' . $row['Designation'] . '"/><br/>';
            echo 'Website : <input name="website" type="text" size=45 value="' . $row['website'] . '"/>';
            echo '<div style="margin-left:20px"><input type="submit" name="submit" value="Update" />';
            echo '</div><br/><br/></p>';
        }

}
else{
	echo 'ERROR!';
}

echo '<hr/><a href="../masterpage.php"> Return to main Admin Page</a>';

?>

<hr/>
        <a href="../logout.php" align='center' >Logout</a><br/><p>
        <img src="../images/mysqls.png" alt="MySQL Powered"> Stream Server status: <span id="cc_stream_info_server"></span></p>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</body>
</html>
