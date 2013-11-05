<?php
///////////////////////////////////////////////
// MAKE M3U
//
// Version 1.08.01.06
//
// Copyright (C) JOERG KRUEGER 
//
// Contact: www.codingexpert.de 
///////////////////////////////////////////////
error_reporting(0);
if (isset($_REQUEST['streamURL'])):
	$streamURL = $_REQUEST['streamURL'];
	ob_start();
	header('Content-type: application/m3u');
	header('Content-Disposition: filename="'.time().'.m3u"');
	header("Content-length: 4000");
	ob_end_flush();
	echo $streamURL;
endif;
?>