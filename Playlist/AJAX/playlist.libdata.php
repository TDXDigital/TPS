<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../barcode/validate.php';

session_start();
$json_arr=array();
$code = filter_input(INPUT_GET,'code',FILTER_SANITIZE_NUMBER_INT)?:
        filter_input(INPUT_POST,'code',FILTER_SANITIZE_NUMBER_INT);
//$type = addslashes(filter_input(INPUT_POST, 'type',FILTER_SANITIZE_STRING));//addslashes($_GET['type']);

if(validate_UPCABarcode($code)||  validate_EAN13Barcode($code)){
    $code = substr($code, 1,10);
    $code = ltrim($code,'0');
}

include_once '../../TPSBIN/functions.php';
include_once '../../TPSBIN/db_connect.php';

$query = "SELECT library.*, recordlabel.Name as LabelName, recordlabel.Size as LabelSize FROM library LEFT JOIN recordlabel on library.labelid=recordlabel.LabelNumber where refcode='$code' limit 1;";
$json_result = array();

$result=$mysqli->query($query);
if($mysqli->error){
    http_response_code(500);
    die($mysqli->error);
}
while($row = $result->fetch_array(MYSQLI_ASSOC)){
    foreach ($row as $key => $val){
        $json_result[$key]=$val;
    }
}
print json_encode($json_result);
$result->free();
$mysqli->close();