<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	die('data.setCell(0, 0, -1);');
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){echo 'data.setCell(0, 0, -2);';}
	
    }
else{
	echo 'data.setCell(0, 0, -99);';
}

	$adID = 11;//$GET("ID");
	for($i=0;$i<24;$i++){
		echo "[".$i;
		for($x = 0;$x<7;$x++){
			switch ($x){
				case 0:
					$day = "Sunday";
					break;
				case 1:
					$day = "Monday";
					break;
				case 2:
					$day = "Tuesday";
					break;
				case 3:
					$day = "Wednesday";
					break;
				case 4:
					$day = "Thursday";
					break;
				case 5:
					$day = "Friday";
					break;
				case 6:
					$day = "Saturday";
					break;
			}
			$sql = "SELECT count(adrotation.AdId) FROM adrotation,addays WHERE adrotation.AdId='".$adID.":00:00' and ".$i." BETWEEN adrotation.startTime AND adrotation.endTime AND addays.AdIdRef=adrotation.AdId AND addays.Day='".$day."' ";
			$result=mysql_query($sql);
			$var = mysql_fetch_array($result);
			if($var["count(adrotation.AdId)"]=="0"){
				echo ", false
				";
			}
			else{
				echo ", true
				";
			}
		}
		echo "]
		";
		if($i<23){
			echo ", ";
		}
	}
?>