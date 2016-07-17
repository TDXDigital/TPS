<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
           <table width="1000">
           <tr><td colspan="100%">
                   <img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/>
           </td></tr></table>
           <table width="1000" align="center" valign="top" style="background-color:white;">

<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

username=' . $_SESSION["username"]);
	}
else if($con){
  $CALL = $_POST['callsign'];
  if($CALL == 'Choose')
  {
       header('Location: /p2viewstation.php');
  }
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT * from STATION where callsign='" . $CALL . "' order by callsign";
        $result=mysql_query($sql);

        while ($row=mysql_fetch_array($result)) {
          echo "<tr><td colspan=\"100%\" style=\"background-color:pink; font-size:12;\">Station 

information can only be changed by the database administrator at the server itself (ckxuoss)

</td></tr>";
            echo '<tr><th colspan="2">Station 

Name</th><th>Callsign</th><th>Frequency</th><th>Request Line</th><th>Director 

Phone</th><th>Designation</th><th>Website</th></tr>';
            echo '<tr><td colspan="2">' . $row['stationname'] . '</td>';
            echo '<td>' . $row['callsign'] . '</td>';
            echo '<td>' . $row['frequency'] . '</td>';
//            echo 'Address : ' . $row['address'] . '<br/>';
            echo '<td>' . $row['boothphone'] . '</td>';
            echo '<td>' . $row['directorphone'] . '</td>';
            echo '<td>' . $row['Designation'] . '</td>';
            echo '<td><a href="http://' . $row['website'] . '" target="top"> ' . $row['website'] . ' 

</a></td></tr>';
            echo '<tr height="50"><th colspan="2">Address</th><td colspan="6">' . $row['address'] .

'</td></tr>';
        }

}
else{
	echo 'ERROR!';
}
?>

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
        <td colspan="5"></td>
        <td style="text-align:right;">
        <img src="/images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>
        </table>

</body>
</html>
