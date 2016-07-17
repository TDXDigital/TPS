<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>DPL Administration</title>
</head>
<html>
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
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login');}

        $sqldjsel = "select * from dj where Alias='" . $_POST['Alias'] . "'";
        $SQLR = mysql_query($sqldjsel);
        /*if(mysql_num_rows($SQLR)=="0"){
          echo 'Error no results found';
        }*/
        $firstV = mysql_fetch_array($SQLR);
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="4">
        <h2>View DJ</h2>
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
                 <?php echo $firstV['djname']; ?>
             </td>
             <td>
                 <?php echo $firstV['Alias']; ?>
             </td>
             <td>
                 <?php echo $firstV['active']; ?>
             </td>
             <td>
                 <?php echo $firstV['years'];?>
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
          if($_SESSION['usr']=='user')
          {
            echo "<form name=\"exit\" action=\"../VERLogout.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Exit\">";
            echo "</form>";
            echo "</td><td colspan=\"5\">";
			}
          else
          {
            echo "<form name=\"logout\" action=\"../logout.php\" method=\"POST\">";
			echo "<input type=\"submit\" value=\"Logout\"></form></td>";
            echo "<td><form name=\"main\" action=\"../masterpage.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Return\">";
            echo "</form></td>";
			echo "<img src=\"../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></tr>";
          }
        ?>

        </table>

<script type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</body>
</html>
