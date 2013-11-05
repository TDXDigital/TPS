<?php
    session_start();

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("posts")){header('Location: /login.php');}
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/altstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="/masterpage.php"><img src="/images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Remove Post</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="/Poster/p2remove.php">
		<table border="0" class="tablecss">
			<tr>
				<th>
					Header
				</th>
				<th>
					Auhor
				</th>
				<th>
					Content
				</th>
				<th>
					Visible
				</th>
			</tr>
			<tr>
				<td>
					<input name="header" type="text" size="25%" />
				</td>
				<td>
					<input name="Author" type="text" size="25%" />
				</td>
				<td>
					<input name="content" type="text" size="25%" />
				</td>
				<td>
					<input name="Visible" type="checkbox"  checked="true"/>
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
				<form method="POST" action="/masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="/images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	
<?php

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>