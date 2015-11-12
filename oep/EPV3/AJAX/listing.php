<table style="border-style: none; width: inherit; ">
	<tr><th>Type</th><th>Playlist</th><th>Spoken</th><th>Time</th><th>Title</th><th>Artist</th><th>Album</th><th>Composer</th><th>CC</th><th>Hit</th><th>Ins</th><th>Language</th></tr>
<?php

function to12hour($hour1){ 
	// 24-hour time to 12-hour time 
	return DATE("g:i a", STRTOTIME($hour1));
}
function to24hour($hour2){
	// 12-hour time to 24-hour time 
	return DATE("H:i", STRTOTIME($hour2));
}

session_start();
$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
		die("<h2>Error " . mysql_errno() . "</h2><p>Could not establish connection to database. Authentication failed</p>");
	}
	else{
		if(!mysql_select_db($_SESSION['DBNAME'])){
			die("<h2>Error " . mysql_errno() . "</h2><p>User Access Error. Database refused connection</p>");
		}
	}
$query = "SELECT song.*,language.languageid FROM song,language WHERE song.songid=language.songid and song.starttime='".$_SESSION['time']."' and song.programname='".$_SESSION['program']."' AND song.date='".$_SESSION['date']."' order by starttime desc";
if(!$data=mysql_query($query)){
	die('<tr><td colspan="100%">Error '.mysql_errno().'; '.mysql_error().'</td></tr>');
}
$i = 0;
//echo mysql_num_rows($data);
while($row=mysql_fetch_array($data)){
	if($i%2==1){
		echo "<tr class='listrow'>";
		
	}
	else{
		echo "<tr class='listrowalt'>";
	}
	echo "<td>".$row['category']."</td>";
	echo "<td>".$row['playlistnumber']."</td>";
	echo "<td>".$row['Spoken']."</td>";
	echo "<td>".$row['time']."</td>";
	echo "<td>".$row['title']."</td>";
	echo "<td>".$row['artist']."</td>";
	echo "<td>".$row['album']."</td>";
	echo "<td>".$row['composer']."</td>";
	echo "<td>".$row['cancon']."</td>";
	echo "<td>".$row['hit']."</td>";
	echo "<td>".$row['instrumental']."</td>";
	echo "<td>".$row['languageid']."</td>";
	echo "</tr>";
	$i++;
}
?>
</table>