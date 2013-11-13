<?php
      session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../logout.php');}
}
else{
	echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../altstyle.css" />
	<title>Quickview</title>
</head>
	<body style="font-size: 12px; text-align:left; background-color: white; background-image: none;">
		<table border="1px" style="border-style: groove; padding-left: 1px; padding-right: 1px">
		<tr><th width="20%">Program Name</th><th width="20%">Date</th><th width="20%">Time</th><th width="20%">Finalized</th><th width="20%">Station</th></tr><tr>
		<?php
			list($program, $date, $time, $callsign) = explode("@",$_GET['args']);
			echo "<td>". $program ."</td>";
			echo "<td>". $date . "</td>";
			echo "<td>". $time . "</td>";
			echo "<td>N/A</td>";
			echo "<td>". $callsign . "</td>";
		?>
		</tr></table>
		<table border="1px" style="margin-top:2px; border-style: groove; padding-left: 1px; padding-right: 1px">
			<tr><th>Cat</th><th>Playlist</th><th>Time</th><th>Title</th><th>Artist</th><th>Album</th><th>Composer</th><th>CC</th><th>Hit</th><th>Ins</th><th>Lang</th></tr>
			<?php
				$FETCH = "select * from SONG where callsign='" . addslashes($callsign) . "' and programname='" . addslashes($program) . "' and date='" . addslashes($date) . "' and starttime='" . addslashes($time) . "' order by time , songid ";
				if(!$Result=mysql_query($FETCH)){
					echo "<tr><td>ERROR</td><td>".mysql_errno()."</td><td colspan=\"100%\">".mysql_error()."</td></tr>";
				}
				else{
					while($row = mysql_fetch_array($Result)){
						echo "<tr><td>" . $row['category'] . "</td><td>". $row['playlistnumber'] ."</td><td>".$row['time']."</td><td>" . $row['title'] . "</td><td>" . $row['artist'] . "</td>
						<td>".$row['album']."</td><td>".$row['composer']."</td><td>".$row['cancon']."</td><td>".$row['hit']."</td><td>".$row['instrumental']."</td>";
						echo "</tr>";
					}
				}
			?>
		</table>
	</body>
</html>


