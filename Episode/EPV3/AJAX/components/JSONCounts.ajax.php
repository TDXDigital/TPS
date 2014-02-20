<?php
    include_once "../../../../TPSBIN/functions.php";
    include_once "../../../../TPSBIN/db_connect.php"
    sec_session_start();
	
	function to12hour($hour1){ 
		// 24-hour time to 12-hour time 
		return DATE("g:i a", STRTOTIME($hour1));
	}
	function to24hour($hour2){
		// 12-hour time to 24-hour time 
		return DATE("H:i", STRTOTIME($hour2));
	}
	$EPNUM=$_GET['EPN'];
    $SQL_PL_COUNT = "SELECT 
        (SELECT count(*) FROM song WHERE programname='" . addslashes($EPINFO['programname']) . "' and date='" . addslashes($EPINFO['date']) . "' and starttime='" . addslashes($EPINFO['starttime']). "' and category not like '1%' and category not like '4%' and category not like '5%') AS Total,
        (SELECT count(*) FROM song WHERE programname='" . addslashes($EPINFO['programname']) . "' and date='" . addslashes($EPINFO['date']) . "' and starttime='" . addslashes($EPINFO['starttime']). "' and category not like '1%' and category not like '4%' and category not like '5%' and Playlistnumber IS NOT NULL) AS Count,
        (SELECT round(((Count / Total)*100),2)) AS Percent";
?>