<?php
	include_once "../../../TPSBIN/functions.php";
	if(!is_session_started()){
		sec_session_start();
	}
	echo "<p>Station: " . strtoupper($_SESSION['callsign']) . "</p>"; 
?>
