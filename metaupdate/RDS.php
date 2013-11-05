<?php

 // Copyright James Oliver 2012
 
 // Set Time Zone and Local Variables
 	date_default_timezone_set("America/Edmonton");
	$SERVER = "ckxuradio.su.uleth.ca"; //Should be static IP at UofL, otherwise use hostname
	$USER = "root";
	$RPWCON = "K1w1679";
	
	$StartTime = "00:00:00";
	$Date = date('Y-m-d'); 
	
	$STATIONCALL = "CKXU"; //Change to Station in Database [GLOBAL STATIC]
	$EXCLUDE = "2";
	
	$da = "CKXU 88.3 FM";

	$SQL_Select_Show = "SELECT * FROM episode WHERE callsign = '" . $STATIONCALL . "' and date = '" . $Date . "' Type != '" . $EXCLUDE . "' ORDER BY starttime desc LIMIT 0, 5";
	
	$con = mysql_connect($SERVER,$USER,$RPWCON);
	if($con){
		echo "Connection Established to " . $SERVER;
		if(!$result = mysql_query($SQL_Select_Show)){
			echo "Error Connecting";
			break;
		}
		else{
			$Program = mysql_fetch_array($result);
			$endTime = $Program['endtime'];
			if(date('H:i:s') < $endTime){ // QUERY Logs
				echo "query Logging System";
				//$Name = $Program['programname'];
			}
			else{ // QUERY 24 HR System
				echo "Query 24 Hour Broadcast (Radio DJ) ";
			}
			
			//$da = $Arra['title'];
		}
		
	}
	
		
	$serv["host"][] = "50.7.70.66";
	$serv["port"][] = "8715";
	$serv["passwd"][] = "K1w1679";
	$_SESSION['title']= $da;
	$_SESSION['LastPost']=date("H:i:s");
	$_SESSION['Inactive']=FALSE;
	
    clearstatcache();

    for($count=0; $count < count($serv["host"]); $count++)
    {
    		// RDS DATA
			echo "<p><hr><i>SERVER #".$count." RDS DATA</i><br/>
			SQL Server: ". $SERVER . "<br/>";
			echo "RDS Server: ". $serv["host"][$count];
			echo "<br />RDS Update: " . $da ."<br/></p>";
			$DA2 = str_replace(" ", "%20", $da);
			echo $DA2;
			
        $mysession = curl_init();
        curl_setopt($mysession, CURLOPT_URL, "http://".$serv["host"][$count].":".$serv["port"][$count]."/admin.cgi?mode=updinfo&song=".$DA2);
        curl_setopt($mysession, CURLOPT_HEADER, false);
        curl_setopt($mysession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($mysession, CURLOPT_POST, false);
        curl_setopt($mysession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($mysession, CURLOPT_USERPWD, "admin:".$serv["passwd"][$count]);
        curl_setopt($mysession, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($mysession, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
        curl_setopt($mysession, CURLOPT_CONNECTTIMEOUT, 2);
        curl_exec($mysession);
        curl_close($mysession);
    }
    
    echo "<h1>Shoucast Server RDS Update Sent</h1>";
	echo "<h4>Last Update sent: " . date("H:i:s / d-M-Y") . "</h4>";
	
?>