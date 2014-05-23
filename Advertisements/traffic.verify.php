<?php
include_once "../TPSBIN/functions.php";
include_once "../TPSBIN/db_connect.php";
$STATUS = "Standard Traffic Incomplete";
if(!isset($_SESSION)){
    sec_session_start();
}
$CLIENT_ID=addslashes($_POST['client']);
if(!isset($_POST['client'])){
    header('location:'.$_SERVER['HTTP_REFERER'].'&m=ERROR, Missing Essential Information');
}
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
$mysqli->query("START TRANSACTION");
$mysqli->query("INSERT INTO adverts (Category, Length, EndDate, StartDate, AdName, Active, Friend, Language, ClientID)
VALUES ('$AD_CATEGORY','$AD_LENGTH','$AD_END','$AD_START','$AD_NAME',1,'$AD_FRIEND','$AD_LANG','$CLIENT_ID')");
if($AD_FRIEND=='1'){
    $min_arr = $mysqli->query("SELECT min(Playcount) as min FROM adverts where active='1' and EndDate>CURDATE() and AdId!='".$mysqli->insert_id."' and Friend='1'");    
    $min = $min_arr->fetch_array();
    $mysqli->query("UPDATE adverts SET Playcount='".$min['min']."' where active='1' and Friend='1' and EndDate>CURDATE()");
    if($mysqli->errno!='0'){
        // ROLLBACK if ANY errors occured
        $mysqli->query("ROLLBACK");
        $STATUS="FRIEND ROLLBACK";
    }
    else{
        // Commit if no errors
        $mysqli->query("COMMIT");
        $STATUS="FRIEND TRAFFIC COMPLETE";
    }
}
else{
    $mysqli->query("COMMIT");
        $STATUS="Standard Traffic Complete";
}


if($mysqli->errno=='0'){
    header('location:'.$_SERVER['HTTP_REFERER'].'&m=Traffic%20Created (STATUS: '.$STATUS.')');
}
else{
    echo $mysqli->error;
}
echo "Completed with error:".$mysqli->errno;

?>
