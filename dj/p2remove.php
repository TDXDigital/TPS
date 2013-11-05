<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="/images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php
if($_SESSION['usr']=="User"){
	die('ERROR: Unauthorised Access');
}
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="5">
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
        <?php
             if(!mysql_select_db("CKXU")){header('Location: /login.php');}
             $act = '0';
             if(isset($_POST['active'])){ $act='1';}
             //echo $act . $_POST['active'];
             $sql = "select * from dj where Alias like '" . addslashes($_POST['Alias']) . "' and djname like '".addslashes($_POST['djname'])."'and active like '".$act."' and years like '".addslashes($_POST['years'])."'";

             $result = mysql_query($sql) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
             }
             else{

               while($row=mysql_fetch_array($result)) {
               //begin form
               echo '<form name="newdj" action="/dj/p3remove.php" method="post">';

               //begin row
               echo '<tr height="40"><td>';
               //column 1
                   echo "<input name=\"djname\" type=\"text\" size=\"30\" hidden=\"true\" value=\"" . $row['djname'] . "\">" . $row['djname'];

               echo '</td><td>';
               //column 2
                   echo "<input name=\"Alias\" type=\"text\" size=\"30\" hidden=\"true\" value=\"" . $row['Alias'] . "\">" . $row['Alias'];

               echo '</td><td>';
               //column 3
                   echo "<input name=\"active\" type=\"checkbox\" hidden=\"true\" checked=\"" . $row['active'] . "\">";
				   if($row['active']=='1'){
				   	echo "Yes";
				   }
				   else{
				   	echo "No";
				   }

               echo '</td><td>';
               //column 4
                   echo "<input name=\"years\" type=\"text\" hidden=\"true\" size=\"35\" value=\"" .$row['years'] . "\">" . $row['years'];

               echo "</td><td><input type=\"submit\" value=\"Remove\" /></form>";
               echo'</td></tr>';
               //end row
               }
             }
        echo '</table>';



}
else{
	echo 'ERROR!';
}

?>
<!-- adds horizontal line above return and logout button -->
		<table width="1000">
		<tr><td colspan="5"><hr /></td></tr>
		</table>
		
		<table align="left">
		<tr><td>
	   
        <?php
          if($_SESSION['usr']=='user')
          {
            echo "<form name=\"exit\" action=\"/VERLogout.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Exit\">";
            echo "</form>";
            echo "</td><td colspan=\"5\">";
  }
          else
          {
            echo "<form name=\"logout\" action=\"/logout.php\" method=\"POST\">";
			echo "<input type=\"submit\" value=\"Logout\"></form></td>";
            echo "<td><form name=\"main\" action=\"/masterpage.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Return\">";
            echo "</form></td>";
			echo "<img src=\"/images/mysqls.png\" alt=\"MySQL\" align=\"right\"></tr>";
          }
        ?>
		
        </table>

</body>
</html>