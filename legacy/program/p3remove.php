<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db("CKXU")){header('Location: ../login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT callsign, stationname from STATION order by callsign";
		$VERREM = "SELECT * from episode where programname='".$_POST['pname']."'";
        if(!mysql_query($sql))
        {
          die("Critical Error. The referenced station does not exist in the database. Please contact the DBA now!");
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="5">
        <h2>Remove Program</h2>
        </td></tr>

        <tr><th width="250" colspan="3">
        Program Name
        </th><th width="200">
        Callsign
        </th><th width="200">
        Length (min)
        </th><th width="250">
        Syndicate Source
        </th>
        <th width="100">
        DJ Alias
        </th>
        </tr>
             <tr>
             <td colspan="3">
                 <?php echo $_POST['pname']; ?>
             </td>
             <td>
                 <?php echo $_POST['callsign']; ?>
             </td>
             <td>
                 <?php echo $_POST['length']; ?>
             </td>
             <td>
                 <?php echo $_POST['syndicate'];?>
             </td>
             <td>
                 <?php echo $_POST['dj1'];?>
             </td>
        </tr>
        <tr>
            <td colspan="100%" height="50">
<?php
                   // MySQL Commands
                   $NEWREM = "DELETE PROGRAM.*, PERFORMS.* from PROGRAM, PERFORMS where PROGRAM.callsign=PERFORMS.callsign and PERFORMS.programname=PROGRAM.programname and PERFORMS.callsign='" . $_POST['callsign'] . "' and PERFORMS.programname='" . $_POST['pname'] . "' ";
                   $performs = "delete from program, Performs.Alias where program.callsign='" . addslashes($_POST['callsign']) . "' and program.programname='" . addslashes($_POST['pname']) . "' and performs.programname=program.programname";//performs.Alias='" . $_POST['dj1'] . "'";
                   $sql = "delete from program where programname='" . addslashes($_POST['pname']) . "' and callsign='" . addslashes($_POST['callsign']) . "' and length='" . $_POST['length'] . "' and syndicatesource='" . addslashes($_POST['syndicate']) . "'";
                   $SELE = "select * from episode where programname='" . addslashes($_POST['pname']) . "' and callsign='" . addslashes($_POST['callsign']) . "' ";
				   $SELEP = mysql_query($SELE,$con);
				   //$VERC = mysql_fetch_array($SELEP);
                if($_POST['pname'] == ""){
                  echo '<h4 style="background-color:yellow;">Error: Injection attempt detected.<br />delete not attempted</h4>';
                }
                else if($_POST['dj1'] == ""){
                  echo '<h4 style="background-color:yellow;">Error: Injection attempt detected.<br />delete not attempted</h4>';                }
                }
                else{
                	$SELEP = mysql_query($SELE,$con);
                }
                if(mysql_num_rows($SELEP)=="0"){
                  if(mysql_query($NEWREM)){
                  	 echo '<h5 style="background-color:lightgreen;">This Data was succesfully removed from the database</h5>';
                  }
                  else{
                    if(mysql_errno()=="1451")
                    {
                      echo "<h2 style=\"background-color:red; color:white;\">Error 1451</h2>";
                       echo '<h4 style="background-color:red; color:white;">There exists program logs in archive, Deletion not permitted.<br />Delete present logs before proceding</h4>';
                    }
                    else{
                       echo "<h2 style=\"background-color:red; color:white;\">Error " . mysql_errno() . "</h2>";
                       echo '<h4 style="background-color:red; color:white;">This Data failed to be removed due to a restriction</h4>';
                       echo '<h5 style="background-color:red; color:white;">Error Description: ' . mysql_error() . "</h5>";
                    }
                  }
                }
                else{
                	 echo "<h2 style=\"background-color:red; color:white;\">Error</h2>";
                     echo '<h4 style="background-color:red; color:white;">There exists program logs in archive, Deletion not permitted.<br />Delete present logs before proceding</h4>';
                }


            echo "</td></tr>";

echo "></td></tr></table>\"";

?>
        </td></tr>
</table>
</body>
</html>
