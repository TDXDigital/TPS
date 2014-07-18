<?php
	session_start();

    include_once $_SESSION['BASEREF']."../../TPSBIN/db_connect.php";

    $DEBUG=FALSE;
	$ROOT = addslashes($_GET['q']);
    $BASE = ".";
    $res=array();
    $info=array();
    $MISMATCH=FALSE;
    if($ROOT=='V2'){
        $BASE="./EPV3";
    }
    $fp = fsockopen("ckxu3400lg.local.ckxu.com", 23, $errno, $errstr, 30);
    if (!$fp) {
        echo "$errstr ($errno)<br />\n";
    } else {
        $out = "*0SL";
        fwrite($fp, $out);
        stream_set_timeout($fp,2,0);
          $temp = fread($fp, 8192);
          $res[0] = explode("\n",$temp);
        $info[0] = stream_get_meta_data($fp);
        $out = "*0SS";
        fwrite($fp, $out);
        stream_set_timeout($fp,2,0);
          $res[1] = fread($fp, 8192);
        $info[1] = stream_get_meta_data($fp);
        fclose($fp);
    
        if ($info[0]['timed_out']) {
            echo 'Connection timed out!';
        } else {
            //echo $res[0].$res[1];
        }
    }
    // DO not Need for Now
	//$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
	
	/*if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
	else if($con){
		if(!mysql_select_db($_SESSION['DBNAME'])){die("Error connecting to switch reporting database");}
	}
	else{
		echo 'ERROR! cannot obtain access... this terminal may not be authorised for access';
	}*/
	$sql = "select * from switchstatus ORDER BY ID DESC limit 1 ";
	echo "<span style=\"font-size:9px;\">ACS 8.2 Plus Switch Status</span><table>
	<tr>";
	for($i = 1; $i < 9; $i++){
		echo "<th>" . $i . "</th>";
	}
	echo "<th>S</th></tr><tr>";
    //echo "<span style=\"font-size:9px;\">ACS 8.2 Plus Switch Status</span><br><span>";
	$result = $mysqli->query($sql);
	$srr = mysqli_fetch_array($result);
    if(!empty($res[0][0])&&!empty($res[0][1])&&!empty($res[1])){
        // CHECK BANK 1
        if($srr['Bank1']==$res[0][0]){
            //echo "mATCHES!";
            $bank1=$srr['Bank1'];
        }
        else{
            $MISMATCH=TRUE;
            if($DEBUG){
                echo "<br><div class='ui-state ui-state-error'><span>ERROR (B)</span><br>".$res[0][0]."<br>".$srr['Bank1']."</span></div>";
            }
            if($res[0][0]!=""){
                $bank1=$res[0][0];
            }

        }
        // CHECK BANK 2
        if($srr['Bank2']==$res[0][1]){
            //echo "mATCHES!";
            $bank2=$srr['Bank1'];
        }
        else{
            $MISMATCH=TRUE;
            if($DEBUG){
                echo "<br><div class='ui-state ui-state-error'><span>ERROR (R)</span><br>".$res[0][1]."<br>".$srr['Bank2']."</span></div>";
            }
            if($res[0][1]!=""){
                $bank2=$res[0][1];
            }

        }
        // CHECK BANK 2
        if($srr['SS']==$res[1]){
            //echo "mATCHES!";
            $silence=$srr['SS'];
        }
        else{
            $MISMATCH=TRUE;
            if($DEBUG){
                echo "<br><div class='ui-state ui-state-error'><span>ERROR (S)</span><br>QR: ".$res[1]."<br>DB: ".$srr['SS']."</span></div>";
            }
            if($res[1]!=""){
                $silence=$res[1];
            }

        }
    }
    else{
        
    }
	$track = 0;
    $title = 1;
	for($i = 16; $i > 0; $i=$i-2){
		$dl = substr($bank1,($i*(-1)),1); 	
		if($dl=='0'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/GreenOff.png\" title=\"Switch &#35;1 - $title\" alt=\"0\"/></td>";
		}	
		else if($dl=='1'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/GreenOn.png\" title=\"Switch &#35;1 - $title\" alt=\"1\"/></td>";
		}
		else{
			echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"Switch &#35;1 - $title\" alt=\"2\"/></td>";
		}
		$title++;
	}
    $title = "Broadcast Silence Sensor";
	//$silence = $srr['SS'];
	$SS1 = substr($silence,-1);
	if($SS1 == "0"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$title\" alt=\"0\"/></td>";
	}
	else if($SS1 == "1"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOn.png\" title=\"$title\" alt=\"1\"/></td>";
	}
	else{
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$SS1\" alt=\"2\"/></td>";
	}
	echo "</tr><tr>";
    //echo "</span><br><span>";
    $title = 1;
	for($i = 16; $i > 0; $i=$i-2){
		$dl = substr($bank2,($i*(-1)),1); 	
		if($dl=='0'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/RedOff.png\" title=\"Switch &#35;2 - $title\" alt=\"0\"/></td>";
		}	
		else if($dl=='1'){
			echo "<td><img src=\"$BASE/Images/LIGHTS/RedOn.png\" title=\"Switch &#35;2 - $title\" alt=\"1\"/></td>";
		}
		else{
			echo "<td><img src=\"$BASE/Images/LIGHTS/RedOff.png\" title=\"Switch &#35;2 - $title\"alt=\"2\"/></td>";
		}
		$title++;
	}
	$title = "Record Silence Sensor";
	$SS2 = substr($silence,-2,-1);
	if($SS2 == "0"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$title\" alt=\"0\"/></td>";
	}
	else if($SS2 == "1"){
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOn.png\" title=\"$title\" alt=\"1\"/></td>";
	}
	else{
		echo "<td><img src=\"$BASE/Images/LIGHTS/AmberOff.png\" title=\"$SS2\"/></td>";
	}
    //echo "</span>";
	echo "</tr>";
	echo "</table>";

    $mysqli->query("INSERT into switchstatus (Bank1,Bank2,SS,UID) values ('".$bank1."','".$bank2."','".$silence."','0')");
    $mysqli->close();
	//echo "<span>Timespamp: ".$srr['timestamp']."</span>";
?>