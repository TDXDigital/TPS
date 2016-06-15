<?php
session_start();
date_default_timezone_set($_SESSION['TimeZone']);

?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>TPS Administration</title>
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

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysqli_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysqli_select_db($con, $_SESSION['DBNAME'])){header('Location: /login');}

        $djsql="SELECT * from `dj` order by djname";
        $djresult=mysqli_query($con, $djsql);

        $djoptions="";
        while ($djrow=mysqli_fetch_array($djresult)) {
            $Alias=$djrow["Alias"];
            $name=$djrow["djname"];
            $djoptions.="<OPTION VALUE=\"$Alias\">$name ($Alias)</option>";
        }
        ?>

        <table border="0" height="100" width="100%">
        <tr><td colspan="100%">
        <h2>Edit DJ</h2>
        </td></tr>
        <tr><th colspan="3">
        On-Air Name [% is wildcard]
        </th><th>
        Name
        </th><th>
        Active
        </th><th>
        Start Year
        </th>
        </tr>
             <form name="selections" action="./p2updatedj.php" method="post">
             <tr>
             <td colspan="3">
                 <input name="Alias" type="text" size="40%" value="%"/>
             </td>
             <td>
                 <input name="djname" type="text" size="40%" value="%"/>
             </td>
             <td>
                 <input name="active" type="checkbox" checked/>
             </td>
             <td>
                 <input name="years" type="text" size="25%" value="%"/>
             </td>
            <td>
                <input type="submit" value="Search" />
                </form>
            </td>
        </tr>
        <tr><td colspan="100%"><hr /></td></tr>
        <tr>
        <th colspan="2">
        Direct Selection
        </th>
        </tr>
        <tr>
        <form name="Direct" action="./p3updatedj.php" method="post">
              <td colspan="2">
                 <select name="Alias" size="1">
                         <?php echo $djoptions;?>
                 </select>
             </td>
             <td>
                <input type="submit" value="Edit" />
                </form>
            </td>
        </form>
        </tr>


        <?php

		}
		else{
		echo 'ERROR!';
		}
		?>

	<td colspan="100%"><hr></td>
		<tr><td>

        <form name="logout" action="../logout.php" method="POST">
	<input type="submit" value="Logout"></form></td>
        <td><form name="main" action="/" method="POST">
        <input type="submit" value="Return">
        </form></td>
        <td colspan="4"></td>
	<td><img src="../../images/mysqls.png" alt="MySQL" align="right"></td></tr>
	</table>
        </table>

</body>
</html>
