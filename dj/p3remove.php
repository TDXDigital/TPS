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

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT callsign, stationname from STATION order by callsign";
        if(!mysql_query($sql))
        {
          die("Critical Error, The referenced station does not exist in the database. please contact the DBA now!");
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="100%">
        <h2>Remove DJ</h2>
        </td></tr>

        <tr><th width="250">
        Name
        </th><th width="200">
        Alias
        </th><th width="200">
        Active
        </th><th width="250">
        years
        </th>
        </tr>
             <tr>
             <td>
                 <?php 
                 if(isset($_POST['djname'])){
                   echo $_POST['djname'];
                 }

                 ?>
             </td>
             <td>
                 <?php
                 if(isset($_POST['Alias'])){
                   echo $_POST['Alias']; 
                 }
                 else{
                   echo 'Not Defined. Resubmit Query';
                 }
                 ?>
             </td>
             <td>
                 <?php
                 if(isset($_POST['active'])){
                   echo $_POST['active'];
                 }
                 ?>

             </td>
             <td>
                 <?php
                 if(isset($_POST['years'])){
                   echo $_POST['years'];
                 }
                 ?>
             </td>
        </tr>
        <tr>
            <td colspan="100%" height="50">
                <?php
                   // MySQL Commands
                   $sqlRem = "delete from dj where Alias='" . addslashes($_POST['Alias']) . "'";
                if($_POST['Alias'] == ""){
                  echo '<h4 style="background-color:yellow;">Error: Injection attempt detected.<br />delete not attempted</h4>';
                }
                else{
                  if(mysql_query($sqlRem)){
                             echo '<h5 style="background-color:lightgreen;">This Data was succesfully removed from the database</h5>';
                      }
                  else if(mysql_errno()=="1451"){
                       echo '<h2 style="background-color:red; color:white;">Error: ' . mysql_errno() . '</h2>';
                       echo '<h4 style="background-color:red; color:white;">This dj has a program in this archive, active or in archive</h4>';
                       echo '<p style="background-color:red; color:white;">deletion not allowed, remove all instances of show before continuing<br /><br />Error Details: ' . mysql_error() . '</p>';
                       }
                  else{
                       echo '<h2 style="background-color:red; color:white;">Error: ' . mysql_errno() . '</h2>';
                       echo '<h4 style="background-color:red; color:white;">The deletion failed with the following response:</h4>';
                       echo '<p style="background-color:red; color:white;">Error Description: ' . mysql_error() . '</p>';
                       }
                  }
                ?>
            </td>
        </tr>
        </table>

        <?php

}
else{
	echo 'ERROR!';
}

?>

<table align="center" width="1000">
		<tr><td colspan="5"><hr /></td></tr>
	</table>
	
		<table align="left">
		<tr><td>
		
        <?php
            echo "<form name=\"logout\" action=\"../logout.php\" method=\"POST\">";
	    echo "<input type=\"submit\" value=\"Logout\"></form></td>";
            echo "<td><form name=\"main\" action=\"../masterpage.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Return\">";
            echo "</form></td>";
	    echo "<img src=\"../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></tr>";
        ?>
		
        </table>


</body>
</html>