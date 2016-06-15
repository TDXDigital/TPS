<?php session_start(); ?>
<head>
<link rel="stylesheet" type="text/css" href="/css/phpstyle.css" />
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

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db("CKXU")){header('Location: /login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT callsign, stationname from STATION order by callsign";
        if(!mysql_query($sql))
        {
          die("Critical Error, The referenced station does not exist in the database. please contact the DBA now!");
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="7">
        <h2>Edit Program</h2>
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
        On-Air Name
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
            <td colspan="7" height="50">
                <?php
                   // MySQL Commands
                   $sql = "update program set programname='" . addslashes($_POST['pname']) . "' , callsign='" . addslashes($_POST['callsign']) . "' , length='" . addslashes($_POST['length']) . "' , syndicatesource='" . addslashes($_POST['syndicate']) . "' where programname='" . addslashes($_POST['pnamex']) . "' and callsign='" . addslashes($_POST['callsignx']) . "' and length='" . addslashes($_POST['lengthx']) . "' and syndicatesource='" . addslashes($_POST['syndicatex']) . "'";
                   $performs = "update performs set callsign='" . addslashes($_POST['callsign']) . "' , programname='" . addslashes($_POST['pname']) . "' , Alias='" . addslashes($_POST['dj1']) . "' where callsign='" . addslashes($_POST['callsignx']) . "' and programname='" . addslashes($_POST['pnamex']) . "' and Alias='" . addslashes($_POST['dj1x']) . "'";
                if($_POST['pname'] == ""){
                  echo '<h4 style="background-color:yellow;">Error: The program must have a name.<br />insert not attempted</h4>';
                }
                else if($_POST['dj1'] == ""){
                  echo '<h4 style="background-color:yellow;">Error: The program must have a DJ.<br />insert not attempted</h4>';
                }
                else{
                  if(mysql_query($sql)){
                           if(mysql_query($performs)){
                             echo '<h5 style="background-color:lightgreen;">This Data was succesfully entered into the database</h5>';
                           }
                           else{
                                echo "<h2 style=\"background-color:red; color:white;\">Error " . mysql_errno();
                                echo "<h4 style=\"background-color:red; color:white;\">This Data failed to be entered into the database</h4>";
                                echo '<p style="background-color:red; color:white;">Error Description: ' . mysql_error();
                                mysql_query("Delete from program where pname='" . addslashes($_POST['pname']) . "'",$con);
                           }
                  }
                  else{
                    if(mysql_errno()=="1451")
                    {
                      echo "<h2 style=\"background-color:red; color:white;\">Error 1451</h2>";
                       echo '<h4 style="background-color:red; color:white;">There exists program logs in archive, Major changes not permitted.<br />Delete present logs before proceding</h4>';
                    }
                    else
                    {
                       echo "<h2 style=\"background-color:red; color:white;\">Error " . mysql_errno() . "</h2>";
                       echo '<h4 style="background-color:red; color:white;">This Data failed to be entered into the database</h4>';
                       echo '<h5 style="background-color:red; color:white;">Error Description: ' . mysql_error() . "</h5>";
                    }
                  }
                }
                ?>
            </td>
        </tr>

        <?php

}
else{
	echo 'ERROR!';
}

echo " />
        </td>
     </tr>
     </table>\"";

?>
        </td><tr>

</td></tr>
</table>
</body>
</html>
