<?php
header("Content-type: text/xml");
echo "<?xml version=\"1.0\"?>\n";
echo "<entries>\n";
$psPath = "powershell.exe";
$psDIR = "C:\\WPSScripts\\WEBS\\";
$psScript = "WebPoll.ps1";
$runScript = $psDIR. $psScript;
$runCMD = $psPath." ".$runScript; 
$output= shell_exec($runCMD);
echo( $output );
echo "</entries>\n"; 
?>