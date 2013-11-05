<?php
	session_start();
	echo "<p>Station: " . strtoupper($_SESSION['callsign']) . "</p>"; 
?>