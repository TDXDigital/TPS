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
	$SQL1 = "select count(playlistnumber), cancon, playlistnumber, artist, album from song where date between '" . $_POST['from'] . "' and '" . $_POST['to'] . "' ";
	$SQL2 = "";
	if(isset($_POST['exempt'])){
		$EXEM = $_POST['exempt'];
		for($iex=0 ; $iex < sizeof($EXEM); ++$iex){
			$SQL2 .= "and programname!='" . addslashes($EXEM[$iex]) . "' "; 
		}
	}
	
	//INSERT EXCLUDE HERE
	$SQL3 = "group by playlistnumber order by count(playlistnumber) desc, playlistnumber asc";
	$SQLM = $SQL1 . $SQL2 . $SQL3; 
	
	$arr = mysql_query($SQLM) or die(mysql_error());
	$Resu = "";
	$chnum = 1;
	while ($row = mysql_fetch_array($arr)) {
		if($row['count(playlistnumber)']!=0){
			if($chnum%2){
				$Resu .= "<tr style=\"background-color: #DAFFFF;\">";
			}
			else{
				$Resu .= "<tr>";
			}
			$Resu .= "<td align=center>" . $chnum . "</td><td align=center>". $row['playlistnumber'] . "</td><td align=center>" . $row['count(playlistnumber)'] . "</td>
			<td align=center >" . $row['artist'] . "</td><td align=center>" . $row['album'] . "</td></tr>";
			++$chnum;
		}
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
		<h2>Playlist Report</h2>
	</div>
	<div id="content">
		<table>
			<tr>
				<th width="5%">Chart Number</th>
				<th width="10%">Playlist Number</th>
				<th width="10%">Times Played</th>
				<th width="37.5%">Artist</th>
				<th width="37.5%">Album</th>
			</tr>
			<?php echo $Resu; ?>
		</table>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="button" value="Search" onclick="window.location.href='/Reports/PlaylistRep.php'"></td><td>
				<input type="button" value="Refresh" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
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