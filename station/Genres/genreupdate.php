<?php
session_start();
$link = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if(!$link){
    header('location: '.$_SERVER["HTTP_REFERER"].'?e=Database%20connection%20error');
}
else{

    // FETCH DATA
    $UID   = $_POST['UID[]'];
    $NAME  = $_POST['C_Name[]'];
    $CAN_N = $_POST['C_Cancon[]'];
    $CAN_P = $_POST['C_CCPerc[]'];
    $CAN_T = $_POST['C_CCType[]'];
    $PLA_N = $_POST['C_Playlist[]'];
    $PLA_P = $_POST['C_PlPerc[]'];
    $PLA_T = $_POST['C_PlType[]'];

    // CHECK FOR DATA VALIDITY

    // UPDATE ROWS VIA TRANSACTION
    /*
    while()
    $existing = $mysqli->query("SELECT genreid FROM genre where UID='")
    if()
    // Start Transaction
    $mysqli->autocommit(FALSE);

    //QUERIES FOR TRANSACTION
    $mysqli->query("UPDATE program ");*/

    // CLOSE DB CONNECTION
    $link->close();
}
?>