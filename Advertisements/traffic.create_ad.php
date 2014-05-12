<?php
include_once "../TPSBIN/functions.php";
include_once "../TPSBIN/db_connect.php";
if(!isset($_SESSION)){
    sec_session_start();
}
$CLIENT_ID=addslashes($_POST['client']);
$AD_CATEGORY=addslashes($_POST['category']);
$AD_START=addslashes($_POST['start']);
if($_POST['end']!=''){
    $AD_END=addslashes($_POST['end']);
}
else
{
    $AD_END='9999-12-31';
}
$AD_LENGTH=addslashes($_POST['length']);
$AD_NAME=addslashes($_POST['name']);
$AD_LANG=addslashes($_POST['language']);
if(isset($_POST['friend'])){
    $AD_FRIEND=1;
}
else{
    $AD_FRIEND=0;
}



if(!isset($mysqli)){
    die("DB_connect_error");
}

$mysqli->query("INSERT INTO adverts (Category, Length, EndDate, StartDate, AdName, Active, Friend, Language, ClientID)
VALUES ('$AD_CATEGORY','$AD_LENGTH','$AD_END','$AD_START','$AD_NAME',1,'$AD_FRIEND','$AD_LANG','$CLIENT_ID')");

if($mysqli->errno=='0'){
    header('location:'.$_SERVER['HTTP_REFERER'].'&m=Traffic%20Created');
}
else{
    echo $mysqli->error;
}
echo "Completed with error:".$mysqli->errno;

?>
