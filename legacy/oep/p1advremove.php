<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
	$GENRE = "SELECT * from GENRE order by genreid asc";
	$GENRES = mysql_query($GENRE,$con);
	$genop = "<OPTION VALUE=\"0\">Any</option>";
	while ($genrerow=mysql_fetch_array($GENRES)) {
        $GENid=$genrerow["genreid"];
        $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
    }
	$djsql="SELECT * from DJ order by djname";
    $djresult=mysql_query($djsql,$con);

    $djoptions="<option value=\"0\">Any</option>";//<OPTION VALUE=0>Choose</option>";
    while ($djrow=mysql_fetch_array($djresult)) {
        $Alias=$djrow["Alias"];
        $name=$djrow["djname"];
        $djoptions.="<OPTION VALUE=\"$Alias\">" . $name . "</option>";
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
		<form name="search" method="POST" action="p2advremove.php">
		<table border="0" class="tablecss">
			<tr>
				<th>
					Program Name
				</th>
				<th>
					Genre
				</th>
				<th>
					Length
				</th>
				<th>
					Syndicate
				</th>
				<th>
					Host
				</th>
				<th>
					Co-Host
				</th>
				<th>
					Callsign
				</th>
			</tr>
			<tr>
				<td>
					<input name="name" type="text" size="25%" />
				</td>
				<td>
					<select name="genre">
						<?php echo $genop;?>
					</select>
				</td>
				<td>
					<input name="length" type="text" maxlength="8" size="8" />
				</td>
				<td>
					<input name="syndicate" type="text" maxlength="25" size="25" />
				</td>
				<td>
					<select name="dj1" disabled="true">
						<?php echo $djoptions;?>
					</select>
				</td>
				<td>
					<select name="dj2" disabled="true">
						<?php echo $djoptions;?>
					</select>
				</td>
				<td>
					<input name="callsign" type="text" maxlength="6" size="6"/>
				</td>
			</tr>
			<tr>
				<th>
					Date From
				</th>
				<th>
					Date To
				</th>
				<th>
					Time From
				</th>
				<th>
					Time To
				</th>
				<th>
					PreRecord
				</th>
				<th>
					Active
				</th>
				<th>
					Empty Episode
				</th>
			</tr>
			<tr>
				<td>
					<input type="date" name="BGDate" />
				</td>
				<td>
					<input type="date" name="ENDate" />
				</td>
				<td>
					<!--<input type="date" name="date" value="<?php
					date_default_timezone_set("UTC");
					echo date('Y-m-d'); ?>" />-->
					<input name="BGTime" type="time" />
				</td>
				<td>
					<input name="ENTime" type="time" />
				</td>
				<td>
					<input type="checkbox" name="Prerec" value="1" />
				</td>
				<td>
					<input type="checkbox" name="active" value="1"/>
				</td>
				<td>
					<input type="checkbox" name="Empty" value="1"/>
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

<?php

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>
