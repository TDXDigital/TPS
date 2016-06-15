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
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}

        $djsql="SELECT * from DJ order by djname";
        $djresult=mysql_query($djsql,$con);

        $djoptions="";
        while ($djrow=mysql_fetch_array($djresult)) {
            $Alias=$djrow["Alias"];
            $name=$djrow["djname"];
            $djoptions.="<OPTION VALUE=\"" . $Alias . "\">" . $name . "</option>";
        }
        ?>

        <table align="left" border="0" height="100" width="1000">
        <tr><td colspan="4">
        <h2>View DJ</h2>
        </td></tr>

        <tr><th width="30%" colspan="2">
        On-Air Name [% is wildcard]
        </th><th width="30%">
        Name
        </th><th width="15%">
        Active
        </th><th width="25%">
        Start Year
        </th>
        </tr>
             <form name="selections" action="p2viewdj.php" method="post">
             <tr>
             <td colspan="2">
                 <input name="Alias" type="text" size="40%" value="%"/>
             </td>
             <td>
                 <input name="djname" type="text" size="40%" value="%"/>
             </td>
             <td>
                 <input name="active" type="checkbox" checked/>
             </td>
             <td>
                 <input name="years" type="text" size="30%" value="%"/>
             </td>
            <td colspan="4">
                <input type="submit" value="Search" />
                </form>
            </td>
        </tr>
        <tr><td colspan="100%"><hr /></td></tr>
        <tr>
        <th>
        Direct Selection
        </th>
        </tr>
        <tr>
        <form name="Direct" action="p3viewdj.php" method="post">
              <td>
                 <select name="Alias">
                         <?php echo $djoptions;?>
                 </select>
             </td>
             <td colspan="1">
                <input type="submit" value="View" />
                </form>
            </td>
        </tr>
        <tr height="5"><td colspan="100%"><hr></td></tr>
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
			echo "<td width=\"100%\"><img src=\"../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></td></tr>";
          }
        ?>

        </table>


	<?php

	}
	else{
	echo 'UNKNOWN ERROR - CONNECTION FAILED';
	}

	?>

</body>
</html>
