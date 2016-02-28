<?php 
session_start(); 
//Used to silence annoying warnings so we can load the proper timezone...
date_default_timezone_set('UTC');
?>
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

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');} 
         // end PHP Header
        ?>
        <table align="left" border="0" height="100" width="1000">
        <tr><td colspan="100%">
        <h2>New DJ</h2>
        </td></tr>
        <tr><th colspan="2" width="40%">
        Name
        </th><th width="35%">
        On-Air Name (optional)
        </th><th width="15%">
        Year Joined
        </th><th width="10%">
        Active
        </th>
        </tr>
             <form name="newdj" action="p2newdj.php" method="post">
             <tr>
             <td colspan="2">
                 <input name="name" type="text" size="50%" required="true" autofocus="true" />
             </td>
             <td>
                 <input name="alias" type="text" size="50%"/>
             </td>
             <td>
                 <input name="year" type="text" size="10%" value="<?php echo date('Y'); ?>"/>
             </td>
             <td>
                 <input name="active" type="checkbox" checked="checked" size="10%"/>
             </td>
        
            <td align="left">
                <input type="submit" value="Create" size="10%"/>
                </form>
            </td>
          </tr>


		  
		   
		<?php
		}		

		else{
			echo 'ERROR!';
		}
		?>
		
		
		<tr><td colspan="100%"><hr /></td></tr>
	
		<tr><td>
	   
        <?php
         
            echo "<form name=\"logout\" action=\"../logout.php\" method=\"POST\">";
			echo "<input type=\"submit\" value=\"Logout\"></form></td>";
            echo "<td><form name=\"main\" action=\"../masterpage.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Return\">";
            echo "</form></td><td colspan=\"3\"></td>";
			echo "<td><img src=\"../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></td></tr>";
        ?>
		
        </table>

</td></tr>
</table>
</body>
</html>