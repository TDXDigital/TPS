<?php
/*
Installation:
- Edit the server details below;
- If using this script on a webserver and the MySQL database on another server, make sure that the
MySQL user has access from your webserver and the firewall ports are open both on the webserver and on your MySQL server computer;

For detailed info about creating MySQL users, please read this:
http://dev.mysql.com/doc/refman/5.5/en/adding-users.html

Use it at your own risk!

*/

/* ============================================================================= */
// EDIT BELOW
/* ============================================================================= */

$stationName	= "CKXU 88.3 FM";		//Your Station name

$mysql_server	= "172.22.10.50";				// MySQL Server's IP (and port if not default). Ex: (191.268.1.2 or 191.268.1.2:345).
$mysql_database	= "radiodj164_2";				// MySQL database name
$mysql_user		= "root";					// MySQL database username. Usually root.
$mysql_pass		= "88.3ForLife";						// MySQL database password.

$track_repeat	= 480;						// Don't display the track if it was played in the last X minutes.
$artist_repeat	= 480;						// Don't display the track if the artist was played in the last X minutes.
$def_timezone	= 'America/Edmonton';		// Set your time-zone.

/* ============================================================================= */
// END EDIT
/* ============================================================================= */

function track_can_play($tr_played, $art_played){
	global $track_repeat, $artist_repeat;
	
	//this should work on any php version
	/*
	$date1 = strtotime($tr_played);
	$date2 = time();
	$subTime = $date1 - $date2;
	$tr_min = round(abs($subTime/60));
	
	$date1 = strtotime($art_played);
	$date2 = time();
	$subTime = $date1 - $date2;
	$ar_min = round(abs($subTime/60));

	*/
	
	//the following version is for php > 5.3
	
	$track_played = new DateTime($tr_played);
	$artist_played = new DateTime($art_played);
		
	$track_diff = $track_played->diff(new DateTime(date('Y-m-d H:i:s', time())));
	$artist_diff = $artist_played->diff(new DateTime(date('Y-m-d H:i:s', time())));
		
	$tr_min = $track_diff->days * 24 * 60;
	$tr_min += $track_diff->h * 60;
	$tr_min += $track_diff->i;
		
	$ar_min = $artist_diff->days * 24 * 60;
	$ar_min += $artist_diff->h * 60;
	$ar_min += $artist_diff->i;
	
	
		
	if($tr_min > $track_repeat && $ar_min > $artist_repeat){
		return true;
	}else{
		return false;
	}
}

function db_conn() {
	global $opened_db, $mysql_server, $mysql_user, $mysql_pass, $mysql_database;

	@$opened_db = mysql_connect($mysql_server, $mysql_user, $mysql_pass);
	
	if (!$opened_db) {
	
		echo '<p>For the moment, our server is off-line.<br />Please come back later...</p>';
		require_once('footer.php');
		
		die();
		
	} else {
		@mysql_select_db($mysql_database) 
			or die(mysql_error());
	}
}
	
function db_close($opened_db) {
	@mysql_close($opened_db);
}

?>