<?php

/* ============================================================================= */
// EDIT BELOW
/* ============================================================================= */

$pageTitle     = "Song Requests";			//Page title
$limit         = 25; 						//How many upcoming tracks to display?
$targetpage    = $_SERVER['SCRIPT_NAME']; 	//Link to this page
$reqLimit      = 5; 						//Limit number of requests per IP

//messages:
define("ERROR_TRACKID", "Please select a track in order to send the request!<br /><A HREF=\"javascript:javascript:history.go(-1)\">Go Back</A>");
define("ERROR_USERNAME", "Please enter your name in order to send the request!<br /><A HREF=\"javascript:javascript:history.go(-1)\">Go Back</A>");
define("ERROR_TRACKREQ", "The selected track is already requested.<br />Please try again later, or select another track!");
define("ERROR_LIMITREACHED", "Sorry, but you've reached the request limit for one day.");
define("ERROR_UNKNOWN", "Unknown error occured! Please try again...");

define("MSG_REQSUCCESS", "Your request was succesfully placed!");
define("MSG_NORESULTS", "No results to display...");

define("REQ_DESCRIPTION", "Please enter request details bellow");
define("REQ_NAME", "Your Name:");
define("REQ_MESSAGE", "Message (Optional):");
define("REQ_BUTTON", "Submit Your Request");

define("NAV_NEXT", "NEXT");
define("NAV_PREV", "PREVIOUS");

define("SEARCH_TXT", "Search artist or title:");
define("SEARCH_BUTTON", "Search");

define("COL_ARTIST", "Artist");
define("COL_TITLE", "Title");
define("COL_DURATION", "Duration");
define("COL_REQ", "Req");
define("ALT_REQ", "Request this track");

/* ============================================================================= */
// END EDIT
/* ============================================================================= */

require_once('serv_inc.php');
require_once('header.php');

date_default_timezone_set($def_timezone);

//============  FUNCTIONS ==============//

//build duration string from seconds
function convertTime($seconds) {
	$sec = $seconds;
	// Time conversion
	$hours = intval(intval($sec) / 3600);
	$padHours = True;
	$hms = ($padHours)
		? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
		: $hours. ':';
	$minutes = intval(($sec / 60) % 60);
	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
		$seconds = intval($sec % 60);
	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

	return $hms;
}

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

//============ END FUNCTIONS ==============//

$srch = "";		//search term value holder
$srchpath = ""; //search path holder
$srcquery = ""; //search query holder
$stages = 3; 	//how to split the pagination
$page = 1; 		//default page
$reqid = "";

if(isset($_GET['searchterm'])){
	if($_GET['searchterm'] != "") {
	
		$srch = mysql_escape_string($_GET['searchterm']);
		$srchpath = "&searchterm=$srch";
		$srcquery = "AND (`artist` LIKE '%$srch%' OR `title` LIKE '%$srch%')"; //Search artist and title
	}
}

//Get the page if it's requested
if(isset($_GET['page'])){
	$page = mysql_escape_string($_GET['page']);
}

if($page){
	$start = ($page - 1) * $limit;
}else{
	$start = 0;
}

if(isset($_POST['reqsubmit'])){

	/*
	ERROR CODES:
	0 = No error
	1 = no user name
	2 = no requested track
	3 = track already in queue
	4 = request limit reached
	*/

	$reqname = mysql_escape_string($_POST['requsername']);
	$reqmsg = mysql_escape_string($_POST['reqmessage']);
	$reqsongID = mysql_escape_string($_POST['songID']);
	$reqIP = getRealIpAddr();
	
	$error = 0;
	$reccount = 0;
		
	if(!$reqname){$error = 1;}
	if(!$reqsongID){$error = 2;}
	
	if($error == 0){
	
		db_conn();
		
		//track is already requested?
		$recheck = "SELECT COUNT(*) AS num FROM `requests` WHERE `songID`='$reqsongID' AND `played`=0;";
		$total_req = mysql_fetch_array(mysql_query($recheck));
		
		if($total_req['num'] > 0){
			$error = 3;
		}
		
		@mysql_free_result($total_req);
		
		if($error == 0){
			//user has reached the request limit?
			$recheck = "SELECT COUNT(*) AS num FROM `requests` WHERE `userIP`='$reqIP' AND DATE(`requested`) = DATE(NOW());";
			$total_req = mysql_fetch_array(mysql_query($recheck));
			
			if($total_req['num'] >= $reqLimit){
				$error = 4;
				$reccount = $total_req['num'];
			}
			
			@mysql_free_result($total_req);
			db_close($opened_db);
		}
		
	}
	
	switch ($error) {
		case 0:
			db_conn();
			
			$queryx = "INSERT INTO `requests` SET `songID`='$reqsongID', `username`='$reqname', `userIP`='$reqIP', `message`='$reqmsg', `requested`=now();";
			$resultx = mysql_query($queryx);
				
			if($resultx > 0) {
				echo "<div class=\"noticediv\">" . MSG_REQSUCCESS . "</div><br />";
			} else {
				echo "<div class=\"errordiv\">" . ERROR_UNKNOWN . "</div><br />";
			}
				
			@mysql_free_result($resultx);
			db_close($opened_db);
			
			break;
		case 1:
			echo "<div class=\"errordiv\">" . ERROR_USERNAME . "</div><br />";
			break;
		case 2:
			echo "<div class=\"errordiv\">" . ERROR_TRACKID . "</div><br />";
			break;
		case 3:
			echo "<div class=\"errordiv\">" . ERROR_TRACKREQ . "</div><br />";
			break;
		case 4:
			echo "<div class=\"errordiv\">" . ERROR_LIMITREACHED . " (" . $reccount . "/" . $reqLimit . ")" . "</div><br />";
			break;
	}
	
	
	$reqid = "";
}

//Get the page if it's requested
if(isset($_GET['requestid'])){

	if($_GET['requestid'] != "") {
		
			$reqid = mysql_escape_string($_GET['requestid']);
		
			echo "<div class=\"requestcontainer\">\n";
			echo "	<form id=\"formrequest\" name=\"formrequest\" method=\"post\" action=\"$targetpage?page=$page$srchpath\">\n";
			echo "			<table align=\"center\" width=\"500\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n";
			echo "				<tr>\n";
			echo "					<td colspan=\"2\"><div align=\"center\"><p>" . REQ_DESCRIPTION . "</p></div></td>\n";
			echo "				</tr>\n";
			echo "				<tr>\n";
			echo "					<td>" . REQ_NAME . "</td>\n";
			echo "					<td><input type=\"text\" name=\"requsername\" /></td>\n";
			echo "				</tr>\n";
			echo "				<tr>\n";
			echo "					<td valign=\"top\">" . REQ_MESSAGE . "</td>\n";
			echo "					<td><textarea name=\"reqmessage\" cols=\"43\" rows=\"5\"></textarea></td>\n";
			echo "				</tr>\n";
			echo "				<tr>\n";
			echo "					<td colspan=\"2\"><div align=\"center\"><input type=\"Submit\" name=\"reqsubmit\" value=\"" . REQ_BUTTON . "\" /></div></td>\n";
			echo "				</tr>\n";
			echo "			</table>\n";
			echo "			<INPUT TYPE=\"hidden\" name=\"songID\" value=\"$reqid\">\n";
			echo "		</form>\n";
			echo "	</div>\n";
	}
}

//================//

if($reqid == ""){
	db_conn();

	//Get the number of items
	$query = "SELECT COUNT(*) as num FROM `songs` WHERE `enabled`='1' $srcquery AND `song_type`=0";
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages['num'];

	//Get page data
	$query1 = "SELECT `ID`, `artist`, `title`, `duration`, `date_played`, `artist_played` FROM `songs` WHERE `enabled`='1' $srcquery AND`song_type`=0 ORDER BY `artist` ASC LIMIT $start, $limit";
	$result = mysql_query($query1);
		
	// Initial page num setup
	if ($page == 0){$page = 1;}
	$prev = $page - 1;
	$next = $page + 1;
	$lastpage = ceil($total_pages/$limit);
	$LastPagem1 = $lastpage - 1;

	$paginate = '';

	if($lastpage > 1) {	
		$paginate .= "<div class='paginate'>";
		// Previous
		if ($page > 1){
			$paginate.= "<a href='$targetpage?page=$prev$srchpath'>" . NAV_NEXT . "</a>";
		}else{
			$paginate.= "<span class='disabled'>" . NAV_PREV . "</span>";
		}
		
		// Pages
	
		if ($lastpage < 7 + ($stages * 2)) {	
			for ($counter = 1; $counter <= $lastpage; $counter++) {
				if ($counter == $page){
					$paginate.= "<span class='current'>$counter</span>";
				}else{
					$paginate.= "<a href='$targetpage?page=$counter$srchpath'>$counter</a>";
				}					
			}
		} elseif($lastpage > 5 + ($stages * 2))	{
		
		// Beginning only hide later pages
			if($page < 1 + ($stages * 2)) {
				for ($counter = 1; $counter < 4 + ($stages * 2); $counter++) {
					if ($counter == $page){
						$paginate.= "<span class='current'>$counter</span>";
					}else{
						$paginate.= "<a href='$targetpage?page=$counter$srchpath'>$counter</a>";
					}					
				}
			
				$paginate.= "...";
				$paginate.= "<a href='$targetpage?page=$LastPagem1$srchpath'>$LastPagem1</a>";
				$paginate.= "<a href='$targetpage?page=$lastpage$srchpath'>$lastpage</a>";
				
			} elseif($lastpage - ($stages * 2) > $page && $page > ($stages * 2)){
			
				$paginate.= "<a href='$targetpage?page=1$srchpath'>1</a>";
				$paginate.= "<a href='$targetpage?page=2$srchpath'>2</a>";
				$paginate.= "...";
				
				for ($counter = $page - $stages; $counter <= $page + $stages; $counter++){
					if ($counter == $page){
						$paginate.= "<span class='current'>$counter</span>";
					}else{
						$paginate.= "<a href='$targetpage?page=$counter$srchpath'>$counter</a>";
					}
				}
				
				$paginate.= "...";
				$paginate.= "<a href='$targetpage?page=$LastPagem1$srchpath'>$LastPagem1</a>";
				$paginate.= "<a href='$targetpage?page=$lastpage$srchpath'>$lastpage</a>";

			} else {
			
				$paginate.= "<a href='$targetpage?page=1$srchpath'>1</a>";
				$paginate.= "<a href='$targetpage?page=2$srchpath'>2</a>";
				$paginate.= "...";
				
				for ($counter = $lastpage - (2 + ($stages * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $page){
						$paginate.= "<span class='current'>$counter</span>";
					}else{
						$paginate.= "<a href='$targetpage?page=$counter$srchpath'>$counter</a>";
					}
				}
			}
		}
					
		// Next
		if ($page < $counter - 1){ 
			$paginate.= "<a href='$targetpage?page=$next$srchpath'>" . NAV_NEXT . "</a>";
		}else{
			$paginate.= "<span class='disabled'>" . NAV_NEXT . "</span>";
		}	
		$paginate.= "</div>";
	}

	//Search box
	echo '<div align="center">';
	echo "<form name=\"input\" action=\"$targetpage\" method=\"get\">";
	echo SEARCH_TXT . " <input type=\"text\" value=\"$srch\" name=\"searchterm\"> <input type=\"submit\" value=\"" . SEARCH_BUTTON . "\">";
	echo '<br />';
	echo '</form>';
	echo '</div>';

	if($total_pages > 0){

		//Add the pagination
		echo "<div align=\"center\">$paginate</div><br />";

		//Results table
		echo '<table class="main_table" border="0" cellspacing="0" cellpadding="5">';
		echo " <tr>" . "\n";
		echo "   <td class=\"header_no\">#</td>\n";
		echo "   <td class=\"header_live\">" . COL_ARTIST . "</td>\n";
		echo "   <td class=\"header_live\">" . COL_TITLE . "</td>\n";
		echo "   <td class=\"header_live\">" . COL_DURATION . "</td>\n";
		echo "   <td class=\"header_live\">" . COL_REQ . "</td>\n";
		echo " </tr>" . "\n";

		$cnt = 1+($limit*$page)-$limit; //Results counter

		//Add results to the table
		while($row = mysql_fetch_assoc($result)) {
			echo " <tr>" . "\n";
			echo "  <td class=\"entry_no\">$cnt.</td>\n";
			echo "  <td>" . $row['artist'] . "</td>\n";
			echo "  <td>" . $row['title'] . "</td>\n";
			echo "  <td class=\"entry_no\">" . convertTime($row['duration']) . "</td>\n";

			if(track_can_play($row['date_played'], $row['artist_played']) == true) {
				echo "  <td class=\"entry_no\"><a href=\"$targetpage?page=$page&requestid=" . $row['ID'] . "\" title=\"" . ALT_REQ . "\">
				<img src=\"images/add.png\" alt=\"" . ALT_REQ . "\" /></a></td>\n";
			}else{
				echo "  <td class=\"entry_no\"><img src=\"images/delete.png\" /></td>\n";
			}
			
			echo " </tr>" . "\n";
			$cnt++;
		}
		
		@mysql_free_result($result);
		db_close($opened_db);
?>

</table>
<br />

<?php

		//Add the bottom pagination
		echo '<div align="center">' . $paginate . '</div>';
	}else{
		echo "<div class=\"errordiv\">" . MSG_NORESULTS . "</div>";
	}
}
	require_once('footer.php');
?>
