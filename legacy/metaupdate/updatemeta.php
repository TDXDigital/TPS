<?php session_start(); ?>
<!DOCTYPE HTML>
<meta http-equiv="refresh" content="15">

<?php
	date_default_timezone_set("America/Edmonton");
	if(date('H:i:s')<"06:00:00"){
		$da="CKXU After Hours";
		$ar="CKXU";
	}
	else{
		$da="CKXU 88.3 FM Lethbridge's True Alternative";
		$ar="Lethbridge's True Alternative";
	}
	if(!isset($_SESSION['title'])){
		$_SESSION['title']="NONE SET F#00000001";
		$_SESSION['LastPost']="00:00:00";
		$_SESSION['Inactive']=FALSE;
	}
    //$da="CKXU 88.3 FM";
	$SERVER = "ckxuradio.su.uleth.ca";
	$USER = "root";
	$RPWCON = "K1w1679";
	$MAXSEARCH = "2";
	$CONN = mysql_connect($SERVER,$USER,$RPWCON);
	if($CONN){
		if(!mysql_select_db("CKXU")){
			echo "<h1 width=\"100%\" style=\"color:white; background-color:red;\">ERROR COULD NOT SET DATABASE</h1>";
		}
		else{
			echo "<h5 width=\"100%\" style=\"background-color:lightgreen;\">Connected to MySQL Server at [" . date("H:i:s / d-M-Y") . "]</h5>";
			$hour=-1;
			$DHR = 0;
			$NOW = strtotime( $hour.' hour');
			$SQL = "SELECT * FROM SONG where date='".date('Y-m-d')."' and time between '" . date('H:i:s',strtotime( $hour.' hour')) . "' and '" . date('H:i:s') . "' and category not like '5%' and category not like '4%' and category not like '1%'";
			while($hour > -$MAXSEARCH){
				$FROM = date('H:i:s',strtotime( $hour.' hour'));
				$TO   = date('H:i:s',strtotime( $DHR.' hour'));
				if( $FROM >= $TO){
					$SQL = "SELECT * FROM SONG where date='".date('Y-m-d' , strtotime($DHR. ' hour'))."' and time between '00:00:00' and '" . $TO . "' and category not like '5%' and category not like '4%' and category not like '1%' order by time desc";
					$SQL2 = "SELECT * FROM SONG where date='".date('Y-m-d' , strtotime($DHR. ' hour'))."' and time between '".$FROM."' and '00:00:00' and category not like '5%' and category not like '4%' and category not like '1%' order by time desc";
					if(!$Result = mysql_query($SQL)){
						echo mysql_errno() . " - " . mysql_error() . "</br>";
					}
					else{
						if(mysql_num_rows($Result)==0)
						{
							if(!$Result = mysql_query($SQL2)){
								echo mysql_errno() . " - " . mysql_error() . "</br>";
							}
						}
						else{
							//echo "found";
							$Arra = mysql_fetch_array($Result);
							$da = $Arra['title'];
							break;
						}
					}
					
				}
				else{
					$SQL = "SELECT * FROM SONG where date='".date('Y-m-d' , strtotime($DHR. ' hour'))."' and time between '" . $FROM . "' and '" . $TO . "' and category not like '5%' and category not like '4%' and category not like '1%' order by time desc";
					if(!$Result = mysql_query($SQL)){
						echo mysql_errno() . " - " . mysql_error() . "</br>";
					}
					else{
						if(mysql_num_rows($Result)!=0)
						{
							//echo "found";
							$Arra = mysql_fetch_array($Result);
							$da = $Arra['title'] . " - " . $Arra['Artist'];
							break;
						}
					}
				}
				--$DHR;
				--$hour;
			}
		}
		
	}
	else{
		echo "<h1 width=\"100%\" style=\"color:white; background-color:red;\">ERROR COULD NOT CONNECT TO SERVER</h1>";
	}
$OVR = FALSE;	
	// CODE FOR UPDATING STREAM TITLE
if($_SESSION['LastPost'] < date("H:i:s",strtotime("-10 minutes")) && $_SESSION!=TRUE){
		$SQP = "Select * from program where date=";
		
	echo "Inactivity Flag, Sending Generic Data";
	echo $_SESSION['LastPost'] ." after ". date("H:i:s",strtotime("-20 minutes"));
	if(date('H:i:s')<"06:00:00"){
		$da="CKXU After Hours";
		$ar="CKXU";
	}
	else{
		$da="CKXU 88.3 FM Lethbridge's True Alternative";
		$ar="Lethbridge's True Alternative";
	}
	$_SESSION['Inactive']=TRUE;
	$OVR=TRUE;
}

if($_SESSION['title']==$da && $OVR==FALSE){
	echo "No New Data, Post Delayed<br/>Last Update:".$_SESSION['LastPost'];
}
else{
		/*********************************/
        //
        // POST TO REMOTE SERVER
        //
        //********************************/
	$serv["host"][] = {"174.36.206.217","142.66.48.28"};
	$serv["port"][] = {"8715","8715"};
	$serv["passwd"][] = {"K!w1679","K1w1679"};
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
}
?>