<?php
session_start();
date_default_timezone_set($_SESSION['TimeZone']);
?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>TPS Administration</title>
</head>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysqli_select_db($con, $_SESSION['DBNAME'])){
          header('Location: ../login.php');
        }
        ?>

        <table align="left" border="0" height="100" width="100%">
        <tr><td colspan="4">
        <h2>Edit DJ</h2>
        </td></tr>

        <tr><th width="250">
        Name
        </th><th width="200">
        On-Air Name
        </th><th width="200">
        Active
        </th><th width="250">
        Year Joined
        </th>
        </tr>
             <tr>
             <td>
                 <?php echo $_POST['name']; ?>
             </td>
             <td>
                 <?php echo $_POST['Alias']; ?>
             </td>
             <td>
                 <?php echo $_POST['active']?"Yes":"No"; ?>
             </td>
             <td>
                 <?php echo $_POST['year'];?>
             </td>
        </tr>
        <tr>
            <td colspan="100%" height="50">
                <?php
                   // MySQL Commands
                   $sql = "update `dj` set djname='" . addslashes($_POST['name']) . "' , Alias='" . addslashes($_POST['Alias']) . "' , years='" .addslashes($_POST['year']) . "' , active='" .addslashes($_POST['active']) . "' where djname='" . addslashes($_POST['namex']) . "' and Alias='" . addslashes($_POST['aliasx']) . "' and active='" . addslashes($_POST['activex']) . "' and years='" . addslashes($_POST['yearx']) . "'";
                if(isset($_POST['djname'])){
                  if($_POST['djname'] == ""){
                    echo '<h4 style="background-color:yellow;">Error: The program must have a name.<br />you are not chuck norris!</h4>';
                  }
                }
                else{
                  if(mysqli_query($con, $sql)){
                       echo '<h5 style="background-color:lightgreen;">DJ Update Success</h5>';
                  }
                  else{
                       echo '<h4 style="background-color:red; color:white;">This Data failed to be entered into the database</h4>';
                       echo '<p style="background-color:red; color:white;">Error Description: ' . mysql_error();
                  }
                }
                ?>
            </td>
        </tr>
        </table>

        <?php

}
else{
	echo 'UNKNOWN ERROR - CONNECTION FAILED';
}

?>

<table align="center" width="1000">
		<tr><td colspan="100%"><hr /></td></tr>
	</table>

		<table align="left">
		<tr><td>

        <?php
            echo "
            <form name=\"main\" action=\"/\" method=\"POST\">
            <input type=\"submit\" value=\"Return\"></form></td><td>
            <form name=\"search\" action=\"p1updatedj.php\" method=\"POST\">
            <input type=\"submit\" value=\"Search\"></form>
            </td><img src=\"../../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></tr>";
        ?>

        </table>
</body>
</html>
