<?php
date_default_timezone_set('UTC');
include_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR."TPSBIN".
        DIRECTORY_SEPARATOR."functions.php";
include_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR."TPSBIN".
                DIRECTORY_SEPARATOR."db_connect.php";

$GENRE = "SELECT * from `genre` order by genreid asc";
$GENRES = $mysqli->query($GENRE);
$genop = "<OPTION VALUE=\"%\">Select Genre</option>";
while ($genrerow=$GENRES->fetch_array(MYSQLI_ASSOC)) {
    $GENid=$genrerow["genreid"];
    $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
}
$djsql="SELECT * from `dj` where active='1' order by djname";
$djresult=$mysqli->query($djsql);

$djoptions="<option value=0>Any</option>";//<OPTION VALUE=0>Choose</option>";
while ($djrow=$djresult->fetch_array(MYSQLI_ASSOC)) {
    $Alias=$djrow["Alias"];
    $name=$djrow["djname"];
    $djoptions.="<OPTION VALUE=\"".$Alias."\">" . $name . "</option>";
}
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>TPS Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="../../../"><img src="<?php print "../../".$_SESSION['logo']; ?>" alt="logo" /></a>
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
					<select name="dj1">
						<?php echo $djoptions;?>
					</select>
				</td>
				<td>
					<select name="dj2" disabled>
						<?php echo $djoptions;?>
					</select>
				</td>
				<td>
					<input name="callsign" type="text" maxlength="6" size="6" />
				</td>
			</tr>
		</table>

		</div>
	<div id="foot" style="bottom: 0; position: fixed; height: 50px; width: 100% ">
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

	<div id="content">
			<h4>Help</h4>
		<span>You can enter a % into the field to enter partial information. ie, if a show you
			wanted to find was called "Best Show Ever" you can put "Best%" and the system will find all shows that begin with "Best", otherwise you can put %show% to
			find any shows that have "show" in the name or "%ever" for shows that end in "ever"</span>

	</div>
	<div style="height: 50px;">&nbsp</div>
</body>
</html>
