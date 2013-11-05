<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /user/login');}
	$GENRE = "SELECT * from GENRE order by genreid asc";
	$GENRES = mysql_query($GENRE,$con);
	$genop = "<OPTION VALUE=\"%\">Select Genre</option>";
	while ($genrerow=mysql_fetch_array($GENRES)) {
        $GENid=$genrerow["genreid"];
        $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
    }
	$djsql="SELECT * from DJ order by djname";
    $djresult=mysql_query($djsql,$con);

    $djoptions="<option value=0>Any</option>";//<OPTION VALUE=0>Choose</option>";
    while ($djrow=mysql_fetch_array($djresult)) {
        $Alias=$djrow["Alias"];
        $name=$djrow["djname"];
        $djoptions.="<OPTION VALUE=\"".$Alias."\">" . $name . "</option>";
    }
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>DPL Administration</title>
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
		<h2>Edit Program Advanced Search</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2advupdate.php">
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
					<input name="name" type="text" size="25%"/>
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
					<input name="callsign" type="text" maxlength="6" size="6" />
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

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>