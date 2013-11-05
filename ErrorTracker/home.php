<?php
    
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="Style/tracker.css" />
<title>Error Tracker</title>
</head>
<html>
<body>
	<div id="header">
		<a href="/"><img src="/images/Ckxu_logo_PNG.png" alt="CKXU" height="100px" /></a>
		<span style="font-size:70px;
		vertical-align: center;
		font:sans-serif;
		margin-left: 10%;
		text-aligh: center;
		">Help Center</span>
	</div>
	<div id="header" style="font-size: 18px; color:red;">
		
	</div>
	<div id="nav">
		<ul id="navbar">
		<li><a href="/">Home</a>
		</li>
		<li><a href="#">Feedback</a><ul>
			<li><a href="#">Report DPL Error</a></li>
			<li><a href="#">Report Suggestion</a></li></ul>
		</li> 
		<li><a href="#">Modification Forms</a><ul>
			<li><a href="#">User Change Form</a></li>
			<li><a href="#">Program Change Form</a></li>
			<li><a href="#">Requirement Change Request</a></li></ul>
		</li>
		<li><a href="#">Library / 24 Hour</a><ul>
			<li><a href="#">24 Hr Conent Complaint</a></li>
			<li><a href="#">Digital Library Error</a></li>
			<li><a href="#">Physical Library Problem</a></li></ul>
		</li>
		<li><a href="#">Security</a><ul>
			<li><a href="#">Safe Space Form</a></li>
			<li><a href="#">Incident Report</a></li>
			<li><a href="#">Emergency Procedures</a></li>
			</ul>
		</li>
		
		
		<!-- ... and so on ... -->
	</ul>
	</div>
	<div id="content">
		<h3>System Status</h3>
		IIS Server Status:<?php
		if(mysql_connect('ckxuradio.su.uleth.ca','user','abuser')){
			echo " <span style=\"color:green;\">ONLINE</span></br>";
		}
		else{
			echo " <span style=\"color:red;\">OFFLINE/UNKNOWN</span></br>";
		}?>
		Digital Program Log Server Status:<?php
		if(mysql_connect('localhost','user','abuser')){
			echo " <span style=\"color:green;\">ONLINE</span></br>";
		}
		else{
			echo " <span style=\"color:red;\">OFFLINE</span></br>";
		}?>
		Error Tracking Server Status:<?php
		if(mysql_connect('localhost','user','abuser')){
			echo " <span style=\"color:green;\">ONLINE</span></br>";
		}
		else{
			echo " <span style=\"color:red;\">OFFLINE</span></br>";
		}?>
		WFBS7 Server Status:<?php
		if(mysql_connect('localhost','user','abuser')){
			echo " <span style=\"color:green;\">ONLINE</span></br>";
		}
		else{
			echo " <span style=\"color:red;\">OFFLINE</span></br>";
		}?>
		Online Stream (CENT4): <span id="cc_stream_info_server" style="color:green;text-transform: uppercase;">OFFLINE/UNKNOWN</span>
	</div>
	<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
	<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</body>
</html>