<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>DPL Administration</title>
</head>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="/masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Playlist Report</h2>
	</div>
	<div id="content">
        <form action="PlaylistRep2.php" method="POST">
		<table>
			<tr>
				<th><label for="from">Date From</label></th>
				<th><label for="to">Date To</label></th>
				<th><label for="limits">Report limit</label></th>
                <th><label for="Confience">Confidence Check</label></th>
			</tr>
			<tr>
				<td>
					<input type="date" id="from" name="from" value="<?php echo date('Y-m-d', strtotime("yesterday - 1 week") ) ?>" />
				</td>
				<td>
					<input type="date" id="to" name="to" value="<?php echo date('Y-m-d', strtotime("yesterday")) ?>" />
				</td>
				<td>
					<!--<label for="limits">Limit Results</label>-->
					<input type="number" name="limit" value="100" id="limits" />
				</td>
                <td>
                    <!--<input type="checkbox" checked id="Confidence" name="Confidence" />-->
                    <select id="Confidence">
                        <option value="soundex">Use Soundex Verification</option>
                        <option value="None" selected>Use No Verification</option>
                    </select>
                </td>
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
				<td style="width:100%; text-align:right;"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	</form>
<?php

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>