<?php
/*

Live SHOUTcast statistics for multiple servers

This script is (C) Bell Online Ltd 2012

If you use this script, please leave the copyright 
notice and link at the bottom of the page or link 
to mixstream.net somewhere on your website. Feel 
free to modify it in any other way to suit your needs.

Version: v1.1

## Updates: 
## 1.1, Added fclose()

*/
/* ---------- General configuration ---------- */

$station_name = "Radio Station Name";

$refresh = "60";  // Page refresh time in seconds. Put 0 for no refresh
$timeout = "1"; // Number of seconds before connecton times out - a higher value will slow the page down if any servers are offline

/* ----------- Server configuration ---------- */

// Note: dont include http://
// Main server: The song title will be taken from this server

$ip[1] = "172.22.10.94"; 
$port[1] = "8000";

/* Relays: Below you can enter more relays / restreams / channels / competitors or anything else */

$ip[2] = "123.456.789.11";
$port[2] = "8080";

$ip[3] = "123.456.789.12";
$port[3] = "8024";

/* ----- No need to edit below this line ----- */
/* ------------------------------------------- */
$servers = count($ip);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
if ($refresh != "0") 
	{
	print "<meta http-equiv=\"refresh\" content=\"$refresh\">\n";
	}
print "<title>$station_name SHOUTcast Stats</title>\n";
?>
<style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333333;
	margin: 5px;
	background-color: #fff;
}
div {
	background-color: #fef9f2;
	border: 1px solid #000;
	padding: 4px;
	margin-bottom: 5px;
	width: 400px;
}
div div {
	margin: 5px;
	border: 0;
	background-color: #fef9f2;
	margin: 5px;
	margin-bottom: 0;
}
h1 {
	font-size: 22px;
	color: #000;
	margin: 2px;
}
h2 {
	font-size: 14px;
	color: #336666;
	margin: 2px;
}
p {
	margin: 5px;
}
a {
	color: #666699;
	text-decoration: none;
}
a:hover {
	color: #993333;
}
div.line {
	border-bottom: 1px dashed #000;
	height: 3px;
	font-size: 1px;
	margin-top: 0;
}
div#blu, div#blu div {
	background-color: #b2bfc0;
}
.red {
	color: #CC0000;
	font-weight: bold;
}
.small {
	font-size: 10px;
}
-->
</style>
</head>
<body>
<?php
$i = "1";
while($i<=$servers)
	{
	$fp = @fsockopen($ip[$i],$port[$i],$errno,$errstr,$timeout);
	if (!$fp) 
		{ 
		$listeners[$i] = "0";
		$msg[$i] = "<span class=\"red\">ERROR [Connection refused / Server down]</span>";
		$error[$i] = "1";
		} 
	else
		{ 
		fputs($fp, "GET /7.html HTTP/1.0\r\nUser-Agent: Mozilla\r\n\r\n");
		while (!feof($fp)) 
			{
			$info = fgets($fp);
			}
		$info = str_replace('<HTML><meta http-equiv="Pragma" content="no-cache"></head><body>', "", $info);
		$info = str_replace('</body></html>', "", $info);
		$stats = explode(',', $info);
		if (empty($stats[1]) )
			{
			$listeners[$i] = "0";
			$msg[$i] = "<span class=\"red\">ERROR [There is no source connected]</span>";
			$error[$i] = "1";
			}
		else
			{
			if ($stats[1] == "1")
				{
				$song[$i] = $stats[6];
				$listeners[$i] = $stats[0];
				$max[$i] =  $stats[3];
				$bitrate[$i] = $stats[5];
				$peak[$i] = $stats[2];
				if ($stats[0] == $max[$i]) 
					{ 
					$msg[$i] .= "<span class=\"red\">";
					}
				$msg[$i] .= "Server is up at $bitrate[$i] kbps with $listeners[$i] of $max[$i] listeners";
				if ($stats[0] == $max[$i]) 
					{ 
					$msg[$i] .= "</span>";
					}
				$msg[$i] .= "\n    <p><b>Listener peak:</b> $peak[$i]";
				}
			else
				{
				$listeners[$i] = "0";
				$msg[$i] = "    <span class=\"red\">ERROR [Cannot get info from server]</span>";
				$error[$i] = "1";
				}
			}
		}
	fclose($fp);
	$i++;
	}
$total_listeners = array_sum($listeners) ;
print "<div id=\"blu\">\n  <div style=\"text-align: center;\">\n    <h1>There are $total_listeners listeners locked</h1>\n  </div>\n</div>\n<div>\n  <div>\n    <p><b>Current song:</b> $song[1]</p>\n  </div>\n</div>\n<div>\n";
$i = "1";
while($i<=$servers)
	{
  	  print "  <div>\n";
if ($max[$i] > 0) 
	{
	$percentage = round(($listeners[$i] / $max[$i] * 100));
	$timesby = (300 / $max[$i]);
	$barlength = round(($listeners[$i] * "$timesby"));
	}
if ($error[$i] != "1") 
	{
?>
    <table width="400"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25%" align="center"><b><a href="http://<?php print $ip[$i] . ":" . $port[$i]; ?>" target="_blank">Server <?php print $i ?></a></b>&nbsp;&nbsp;</td>
        <td width="75%" colspan="3" bgcolor="#eeeeee"><img src="<?php if ($percentage == "100") { print "red-"; } ?>bar.gif" width="<?php print $barlength ?>" height="12" alt="The server is at <?php print $percentage; ?>% capacity"></td>
      </tr>
      <tr>
        <td width="25%">&nbsp;</td>
        <td width="25%">0%</td>
        <td width="25%" align="center">50%</td>
        <td width="25%" align="right">100%</td>
      </tr>
    </table>
<?php
	}
else
	{
?>
    <table width="400"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25%" align="center"><b><a href="http://<?php print $ip[$i] . ":" . $port[$i]; ?>" target="_blank">Server <?php print $i ?></a></b>&nbsp;&nbsp;</td>
        <td width="75%" colspan="3" bgcolor="#eeeeee">&nbsp;</td>
      </tr>
      <tr>
        <td width="25%">&nbsp;</td>
        <td width="25%">0%</td>
        <td width="25%" align="center">50%</td>
        <td width="25%" align="right">100%</td>
      </tr>
    </table>
<?php
	}
print "    <p><b>Status:</b> $msg[$i]</p>\n  </div>\n  <div class=\"line\"> </div>\n";
	$i++;
	}
print "</div>\n";
$time_difference = "0"; // BST: 1 GMT: 0
$time_difference = ($time_difference * 60 * 60);
$time = date("h:ia", time() + $time_difference);
$date = date("jS F, Y", time() + 0);
print "<div>\n  <div>\n    <p><b>Live SHOUTcast statistics:</b> $date, $time</p>\n  </div>\n</div>\n";
?>
<div>
  <div>
    <p class="small" style="float: left;"><a href="http://mixstream.net/" target="_blank">SHOUTcast statistics script</a>. <a href="http://validator.w3.org/check?uri=referer" target="_blank">Valid HTML 4.01 Transitional</a></p>
    <p class="small" style="float: right;">&copy; MixStream.net 2006</p>
  </div>
</div>
</body>
</html>
