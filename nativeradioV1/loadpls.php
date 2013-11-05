<?php
///////////////////////////////////////////////
// PLS
//
// Version 1.08.03.26
//
// Copyright (C) JOERG KRUEGER 
//
// Contact: www.codingexpert.de 
///////////////////////////////////////////////
error_reporting(0);
if (isset($_REQUEST['plsurl'])):
	header('Content-type: text/plain');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0');
	$plsurl = $_REQUEST['plsurl'];
	$aPathInfo = parse_url($plsurl);
	$sHost = $aPathInfo['host'];
	$sPort = empty($aPathInfo['port']) ? 80 : $sPort = $aPathInfo['port'];
	$sQuery = empty($aPathInfo['query']) ? '' : $sQuery = "?".$aPathInfo['query'];
	$sServiceURI = $aPathInfo['path'] . $sQuery ;
	$sInput = "";
	$sOutput = "";
	$fp = fsockopen($sHost, $sPort, $errno, $errstr);
	if ( ! $fp ):
		echo "ERROR";
	else:
		fputs( $fp, "GET $sServiceURI  HTTP/1.0\r\n" ) ;
		fputs($fp, "Host: $sHost\r\n"); 
		fputs($fp, "User-Agent: PHP Script\r\n" );
		fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n" );
		fputs($fp, "Content-Length: 0\r\n" );	
		fputs($fp, "Connection: Close\r\n\r\n");  
		fwrite( $fp, $out ) ;
		while ( ! feof($fp) ):
			$sInput = $sInput.fgets( $fp, 128 ) ;
		endwhile;
		fclose($fp);
		$helper_array = explode("\n", $sInput);
		for ($i=0;$i<count($helper_array);$i++) {
			if (substr($helper_array[$i], 0, 4)=="File"):
				$temp1_array = explode('=', $helper_array[$i]);
				$sOutput = $sOutput.sonderzeichen($temp1_array[1])."¦";
			endif;
		}
		$sOutput = substr($sOutput, 0, strlen($sOutput)-1);
		echo "&streamurl=".$sOutput;
	endif;
endif;
function sonderzeichen($text){
	//FLASH HAVE PROBLEM WITH SIGNS: + & % " \ '
	return str_replace("+","%2B",str_replace("&","%26",str_replace("%","%25", str_replace("\r","",$text))));
}
?>