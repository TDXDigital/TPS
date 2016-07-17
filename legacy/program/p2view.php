<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
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
        ?>

        <table align="left" border="0" height="100" width="100%">
        <tr><td colspan="100%">
        <h2>View Program</h2>
        </td></tr>

        <tr><th width="30%" colspan="3">
        Program Name
        </th><th width="10%">
        Callsign
        </th><th width="15%">
        Length (min)
        </th><th width="20%">
        Syndicate Source
        </th><th width="25%">
        Host(s)
        </th>
        </tr>
        <?php
             if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}

             //$sql = "select program.*, performs.Alias from program, performs where performs.Alias like '" . $_POST['dj1'] . "' and performs.callsign like '".$_POST['callsign']."'and length like '".$_POST['length']."' and syndicatesource like '".$_POST['syndicate']."' and program.programname like '".$_POST['name']."' and program.programname=performs.programname order by program.programname";
			 $sql = "select program.* from program where callsign like '" . addslashes($_POST['callsign']) . "' and programname like '" . addslashes($_POST['name']) . "' and length like '" . addslashes($_POST['length']) . "' and syndicatesource like '" . addslashes($_POST['syndicate']) . "' order by programname";
             $result = mysql_query($sql) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
             }
             else{

               while($row=mysql_fetch_array($result)) {

               //begin row
               echo '<tr><td colspan="3">';
               //column 1
                   echo $row['programname'];

               echo '</td><td>';
               //column 2
                   echo $row['callsign'];

               echo '</td><td>';
               //column 3
                   echo $row['length'];

               echo '</td><td>';
               //column 4
                   echo $row['syndicatesource'];

               echo '</td><td>';
               //$djrow = mysql_fetch_array($djresult);
               //column 5

               $djname = mysql_query("SELECT Alias from PERFORMS where callsign LIKE '" . addslashes($row['callsign']) . "' and programname LIKE '" . addslashes($row['programname']) . "'");
                   $djrow = mysql_fetch_array($djname);
                   echo $djrow['Alias'];
				   while($djrn = mysql_fetch_array($djname)){
				   	echo ", " . $djrn['Alias'];
				   }
                   //echo $row['Alias'];//$djrow['Alias'];

               echo'</td></tr>';
               //end row
               }
             }



}
else{
	echo 'ERROR!';
}

echo '<tr height="20"><td colspan="100%" style="text-align:bottom;"><hr/></td></tr>';

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
        <td>
        <form name="search" action="p1view.php" method="POST">
              <input type="submit" value="Search">
        </form>
        </td>
        <td colspan="3"></td>
        <td style="text-align:right;">
        <img src="../images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>

        </table>
        </td>
        </tr>
        </table>
</body>
</html>
