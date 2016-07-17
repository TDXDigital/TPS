<?php
include_once "../TPSBIN/functions.php";
include_once "../TPSBIN/db_connect.php";
$DEBUG = TRUE;//$_SESSION['DEBUG'];
$STATUS = "Standard Traffic Incomplete";
$ERROR = FALSE;
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
$mysqli->query("START TRANSACTION");
$mysqli->query("INSERT INTO adverts (Category, Length, EndDate, StartDate, AdName, Active, Friend, Language, ClientID)
VALUES ('$AD_CATEGORY','$AD_LENGTH','$AD_END','$AD_START','$AD_NAME',1,'$AD_FRIEND','$AD_LANG','$CLIENT_ID')");
if($AD_FRIEND=='1'){
    $min_arr = $mysqli->query("SELECT min(Playcount) as min FROM adverts where active='1' and EndDate>CURDATE() and StartDate<CURDATE() and AdId!='".$mysqli->insert_id."' and Friend='1'");    
    $min = $min_arr->fetch_array();
    $SQL_Update="UPDATE adverts SET `Playcount`=`Playcount`-".$min['min'].", `last_reset`=now() WHERE `active`='1' and `Friend`='1' and (EndDate>CURDATE() or EndDate=='9999-12-31') and `Playcount`>=".$min['min'];
    $mysqli->query($SQL_Update);
    if($mysqli->errno!='0'){
        error_log("Traffic_Create caused SQL Error(".$mysqli->errno.") ".$mysqli->error);
        error_log("SQL:$SQL_Update");
        // ROLLBACK if ANY errors occured
        $mysqli->query("ROLLBACK");
        $STATUS=$mysqli->errno;
        //"FRIEND ROLLBACK";
        $ERROR=TRUE;
    }
    else{
        // Commit if no errors
        $mysqli->query("COMMIT");
        $STATUS="FRIEND TRAFFIC COMPLETE";
    }
    if($DEBUG){
        error_log("Traffic_Create caused SQL Error(".$mysqli->errno.") ".$mysqli->error);
        error_log("SQL:$SQL_Update");
    }
}
else{
    $mysqli->query("COMMIT");
        $STATUS="Standard Traffic Complete";
}


if($mysqli->errno=='0'&&!$ERROR){
    header('location:'.$_SERVER['HTTP_REFERER'].'&m=Traffic%20Created%20(STATUS:'.$STATUS.')');
}
elseif ($mysqli->errno=='0'&&$ERROR){
    header('location:'.$_SERVER['HTTP_REFERER'].'&e=Traffic%20Failed%20(STATUS:'.$STATUS.')');
}
else{
    echo $mysqli->error;
}
echo "Completed with error:".$mysqli->errno;

?>
