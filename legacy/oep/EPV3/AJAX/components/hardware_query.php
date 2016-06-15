<?php
    
    include_once "../../../../TPSBIN/functions.php";
    include_once "../../../../TPSBIN/db_connect.php";

    sec_session_start();

    $HID = $_GET['HID'];
    $CMD = $_GET['CMD'];
    $TO = $_GET['TO'] ?: 3;
    if ($stmt = $mysqli->prepare("SELECT hardware.ipv4_address, hardware.port, device_codes.Name, device_codes.Command, device_codes.command_type FROM hardware LEFT JOIN device_codes ON hardware.device_code=device_codes.Device and device_codes.device_code WHERE hardware.hardwareid=? and device_codes.device_code=?")) {

        $stmt->bind_param("ii",$HID,$CMD);
        $stmt->bind_result($IPv4, $Port, $Name, $Command, $command_type);
        if($stmt->execute()){
            $stmt->fetch();
            //echo " -- " . $IPv4 . " : " . $Port . " : ". $Name . " : " . $Command . " : " . $command_type . " -- ";
            if($command_type=="0"){
                $fp = fsockopen($IPv4, $Port, $errno, $errstr, 30);
                stream_set_timeout($fp,$TO,0);
                fwrite($fp, $Command."\r\n");
                $res .= fread($fp, 8192);
                $info = stream_get_meta_data($fp);
                fclose($fp);
    
                if ($info['timed_out']) {
                    echo 'TIMEOUT';
                } else {
                    echo $res;
                }
            }
        }
        else{
            echo $mysqli->error();
        }   
    }
    
    //echo "COmPLeTED";
    
/*$data = array(/*
    (object)array(
        'HW_Name' => $,
        '' => 'myfirsttext',
    ),
    (object)array(
        'oV' => 'mysecondvalue',
        'oT' => 'mysecondtext',
    ),*/
//);

//echo json_encode($data);

?>