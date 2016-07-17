<?php
    session_start();
    /*
if($_SESSION["usr"]=="user"){
	header("Location: ../logout.php");
}*/
$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ' username=' . $_SESSION["usr"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
		if($_SESSION["usr"]!="user"){
			$SQL = "DELETE from Episode where callsign='" . addslashes($_POST['callsign']) . "' and date='"
			. addslashes($_POST['date']) . "' and programname='" . addslashes($_POST['program']) . "' and starttime='"
			. addslashes($_POST['time']) . "' ";
		}
		else{
			$SQL = "Select * from station";
		}
    }
?>

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
	<div id="header">
		<a href="../../masterpage.php"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>Remove Program</h2>
	</div>
	<div id="content">
		<p><?php
			if(mysql_query($SQL,$con)){
				echo 'Perminetly Removed Episode...';
			}
			else if(mysql_errno()=="1451"){
				echo "Cannot Remove without Removal of all songs.<br /> This is a known beta issue";
			}
			else{
				echo mysql_errno();
				echo "<br />";
				echo mysql_error();
			}
		?></p>

		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="button" value="Search" onClick="window.location.href='p1advremove.php'"></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content" style="background-color:black; color:yellow;">
			<h4>WARNING</h4>
		<span>Removal of Episode should <strong>ONLY</strong> be done when they are in error or no longer auditable by any and all regulatory entities</span>

	</div>
</body>
</html>
