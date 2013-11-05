<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
/*if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');}*/
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Commercial Management</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Edit Commercial / Promo</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2update.php">
		<table border="0" class="tablecss">
			<tr>
				<th>
					Ad Number
				</th>
				<th>
					Advertiser/Name
				</th>
				<th>
					Category
				</th>
				<th>
					Length
				</th>
				<th>
					Language
				</th>
				<th>
					Active
				</th>
				<th>
					Friend
				</th>
			</tr>
			<tr>
				<td>
					<input name="adnum" type="text" size="10%"/>
				</td>
				<td>
					<input name="name" type="text" size="15%"
				</td>
				<td>
					<select name="category">
						<option value="%"> Any Category </option>
						<option value="53">53, Sponsored Promotion</option>
	           			<OPTION value="52">52, Sponsor Indentification</OPTION>
	           			<OPTION VALUE="51">51, Commercial</OPTION>
	           			<option value="45">45, Show Promo</option>
	           			<option value="44">44, Programmer/Show ID</option>
	           			<option value="43">43, Station ID</option>
	           		</select>
				</td>
				<td>
					<input name="length" type="number" maxlength="5" disabled size="6" />
				</td>
				<td>
					<input name="Language" type="text" size="10"/>
				</td>
				<td>
					<select name="Active">
						<option value="%">  </option>
						<option value="1"> Yes </option>
						<option value="0"> No </option>
					</select>
				</td>
				<td>
					<select name="Friend">
						<option value="%">  </option>
						<option value="1"> Yes </option>
						<option value="0"> No </option>
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
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>You can enter a % into the field to enter partial information. ie, if a show you 
			wanted to find was called "Best Show Ever" you can put "Best%" and the system will find all shows that begin with "Best", otherwise you can put %show% to
			find any shows that have "show" in the name or "%ever" for shows that end in "ever"</span>
		
	</div>
	
<?php
/*
}
else{
	echo 'ERROR!';
}*/
?>
</body>
</html>