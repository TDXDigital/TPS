<?php
date_default_timezone_set("UTC");
    session_start();
if($_SESSION["access"] < 2){
	die([401, "Unauthorized"]);
}
//$con = new mysqli($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../css/altstyle.css" />
<title>Missing Log Report</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<img src="../<?php echo $_SESSION['logo'];?>" alt="Logo" />
	</div>
	<div id="top">
		<h2>Missing Logs</h2>
	</div>
	<div id="content">
		<table>
			<tr>
				<th>Date From</th>
				<th>Date To</th>
			</tr>
			<form action="MissingLogRep2.php" method="POST">
			<tr>
				<td>
					<input type="date" name="from" value="<?php echo date('Y-m-d', strtotime("yesterday - 6 days") ) ?>" />
				</td>
				<td>
					<input type="date" name="to" value="<?php echo date('Y-m-d', strtotime("yesterday") ) ?>" />
				</td>
				<!--<td>
					<label for="limits">Limit Results</label>
					<input type="number" name="limit" value="100" id="limits" />
				</td>-->
			</tr>
		</table>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Search"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
</body>
</html>
