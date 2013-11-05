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
           <td align="center" colspan="2"><img src="../images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
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
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="5">
        <h2>View DJ</h2>
        </td></tr>

        <tr><th width="250">
        Name
        </th><th width="200">
        On-Air Name
        </th><th width="200">
        Active
        </th><th width="250">
        Start Year
        </th>
        </tr>
        <?php
             if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login');}
             $act = '0';
             if(isset($_POST['active']))
             {
               $act='1';
             }
             //echo $act . $_POST['active'];  //DEBUG
             $sql = "select * from dj where Alias like '" . $_POST['Alias'] . "' and djname like '".$_POST['djname']."'and active like '".$act."' and years like '".$_POST['years']."'";

             $result = mysql_query($sql) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
             }
             else{

               while($row=mysql_fetch_array($result)) {
               //begin row
               echo '<tr><td>';
               //column 1
                   echo $row['djname'];

               echo '</td><td>';
               //column 2
                   echo $row['Alias'];

               echo '</td><td>';
               //column 3
                   echo $row['active'];

               echo '</td><td>';
               //column 4
                   echo $row['years'];

               echo'</td></tr>';
               //end row
               }
             }
				//echo "<hr align=\"center\"><tr height=\"20\"><td colspan=\"6\" style=\"text-align:bottom;\"><hr/></td></tr>";
        echo '</table>';
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

<table>		
        <!-- <hr />
        <a href="/logout.php" align='center' >Logout</a><br/> --><td height="10" style="text-align:left">
        
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</td></tr>
</table>
</body>
</html>