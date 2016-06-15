<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once "../../../TPSBIN/functions.php";
include_once '../../../TPSBIN/db_connect.php';

$station = filter_input(INPUT_GET, 's', FILTER_SANITIZE_STRING);

if(session_status()===PHP_SESSION_NONE){
    sec_session_start();
}

if($stmt = $mysqli->prepare("SELECT timezone FROM station WHERE callsign=?")){
    $stmt->bind_param("s",$station);
    $stmt->execute();
    $stmt->bind_result($time_zone);
    $stmt->fetch();
    date_default_timezone_set($time_zone);
    $date=new DateTime();
    $minute=$date->format('i');
    if($minute>30){
        $date->modify("+1 hour");
    }
    $hour = $date->format('H').":00";
    $result=array('zone'=>$time_zone,'default_zone'=>date_default_timezone_get(),'time_ISO'=>date(DATE_ISO8601),'time_date'=>date("Y-m-d") ,'time_hour'=>$hour, 'offset'=>  date_offset_get(new DateTime)/3600,'callsign'=>$station);
    
    echo json_encode($result);
}
else{
    // does not support mysqlind
}