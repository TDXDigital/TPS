<?php

/* ============================================================================= */
// EDIT BELOW
/* ============================================================================= */

$pageTitle = "TCP-IP Commands";

$rdj_host = "172.22.10.50";
$rdj_port = 21;
$password = "88.3ForLife";

/* ============================================================================= */
// END EDIT
/* ============================================================================= */

require_once('serv_inc.php');
require_once('header.php');

if(isset($_POST['submit'])) {

	$command = htmlentities($_POST['command']);
	$args = htmlentities($_POST['argument']);

	echo "<div class=\"noticediv\">Sent Command: " . $command . "</div>\n
	<br />";
	
	function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		  //check ip from share internet
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		  //to check ip is pass from proxy
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function GetResponse($rcode) {
		switch ($rcode) {
			case 200:
				return "OK";
				break;
			case 401:
				return "Wrong Password!";
				break;
			case 400:
				return "Bad Request!";
				break;
		}
	}

	$fp = fsockopen($rdj_host, $rdj_port, $errno, $errstr, 3);
	if (!$fp) {
		echo "<div class=\"noticediv\">Error: " . $errstr . $errno . "</div>\n
		<br />";
	} else {
		$out = getRealIpAddr() . "&" . $password . "&" . $command . "&" . $args . "&";
		fwrite($fp, $out);
		
		while (!feof($fp)) {
			echo "<div class=\"noticediv\">Response: " . GetResponse(fgets($fp, 128)) . "</div>\n 
			<br />";
		}
		fclose($fp);
	}
}

?>

<div align="left" style="padding:10px;">
<form name="comform" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?q=tcpc" method="post">
	Available Commands:
	<hr />
	<input type="radio" name="command" value="PlayPlaylistTrack" onChange="resetArgs()" checked>Play Playlist Track<br />
	<input type="radio" name="command" value="PlayFromIntro" onChange="resetArgs()">Play From Intro<br />
	<input type="radio" name="command" value="Restart" onChange="resetArgs()">Restart<br />
	<input type="radio" name="command" value="Pause" onChange="resetArgs()">Pause<br />
	<input type="radio" name="command" value="Stop" onChange="resetArgs()">Stop<br />
	<input type="radio" name="command" value="SetInput" onChange="resetArgs()">Set Input (0 to disable, 1 to enable)<br />
	<input type="radio" name="command" value="AddTrackToTop" onChange="resetArgs()">Add Track To Top (Track ID as argument)<br />
	<input type="radio" name="command" value="AddTrackToBottom" onChange="resetArgs()">Add Track To Bottom (Track ID as argument)<br />
	<input type="radio" name="command" value="LoadPlaylist" onChange="displayResult()">Load Playlist (Playlist ID as argument) 
	
<script>
function displayResult()
{
var x=document.getElementById("playlists").selectedIndex;
var y=document.getElementById("playlists").options;
document.getElementById("argo").value=y[x].value;
}

function resetArgs()
{
document.getElementById("argo").value=0;
}
</script>
	
	<?php
	db_conn();
	$query = "SELECT * FROM `playlists` ORDER BY `name` ASC;";
	$result = mysql_query($query);
	
	if (!$result) {
		echo mysql_error();
		exit;
	}else{
		echo '<select id="playlists" onChange="displayResult()">';
		while($row = mysql_fetch_assoc($result)) {
			echo '<option value="' . $row['ID'] . '">' . $row['name'] . '</option>';
		}
		echo'</select> ';
	}
	
	?>
	
	<br />
	
	<input type="radio" name="command" value="RefreshEvents" onChange="resetArgs()">Refresh Events<br />
	<input type="radio" name="command" value="PlaycartByNumber" onChange="resetArgs()">Playcart By Number (Player # as argument)<br />
	<input type="radio" name="command" value="SetAutoDJ" onChange="resetArgs()">Set AutoDJ (0 to disable, 1 to enable)<br />
	<input type="radio" name="command" value="SetAssisted" onChange="resetArgs()">Set Assisted (0 to disable, 1 to enable)<br />
	<input type="radio" name="command" value="ClearPlaylist" onChange="resetArgs()">Clear Playlist<br />
	<hr />
	
	Argument: <input id="argo" type="text" name="argument" value="0">
	
	<br />
	<br />
	<input name="submit" type="submit" value="SEND COMMAND NOW!" />
</form>
</div>

<?php require_once('footer.php'); ?>

