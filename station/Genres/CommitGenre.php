<?php
/*
ALTER TABLE `ckxu`.`genre` 
ADD COLUMN `UID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT AFTER `playlistperc`;
ADD COLUMN `PlType` INT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `UID`;
ADD COLUMN `CCType` INT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `PlType`;
ADD COLUMN `STATION` VARCHAR(4) NOT NULL AFTER `CCType`;
*/
/*
NEW STATEMENT:
ALTER TABLE `ckxu`.`genre` 
ADD COLUMN `UID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT AFTER `playlistperc`,
ADD COLUMN `PlType` INT(2) UNSIGNED NOT NULL DEFAULT 1 AFTER `UID`,
ADD COLUMN `CCType` INT(2) UNSIGNED NOT NULL DEFAULT 1 AFTER `PlType`,
ADD COLUMN `Station` VARCHAR(4) NOT NULL AFTER `CCType`,
ADD UNIQUE INDEX `UID_UNIQUE` (`UID` ASC);
*/

session_start();

//  GET CanCon VALUES
$CCN = addslashes($_POST['cangen']);
$CCP = addslashes(floatval($_POST['canper'])/100);
$CCT = addslashes($_POST['cctype']);

// GET PLAYLIST VALUES
$PLN = addslashes($_POST['plgen']);
$PLP = addslashes(floatval($_POST['plperc'])/100);
$PLT = addslashes($_POST['pltype']);

// GET STATION
$STN = addslashes($_POST['station']);

// GET NAME
$NAME = addslashes($_POST['name']);
$UID = addslashes($_POST['UID']);

$SQL_INSERT = "INSERT INTO genre (genreid,cancon,playlist,canconperc,playlistperc,CCType,PlType,Station) VALUES ('$NAME','$CCN','$PLN','$CCP','$PLP','$CCT','$PLT','$STN')";
$SQL_QUERY_NAME = "SELECT UID, count(uid) AS UID_NUM FROM genre WHERE `genreid`='$NAME'";

$link = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if(!$link){
    header('location: '.$_SERVER["HTTP_REFERER"].'?e=Database%20connection%20error');
}
else{
    if($vars = mysqli_query($link,$SQL_QUERY_NAME)){
        if($vars->fetch_object()->UID_NUM==0){
            if(mysqli_query($link,$SQL_INSERT)){
                header('location: '.$_SERVER["HTTP_REFERER"].'?Genre='.mysqli_insert_id.'&m=Successfully%20Added');
            }
            else{
                //header('location: '.$_SERVER['HTTP_REFERER'].'?e='.mysqli_error($con));
                echo $SQL_INSERT;
                echo "</br>".mysqli_error($link);
            }
        }
        else{
            //header('location: '.$_SERVER['HTTP_REFERER'].'?e='.mysqli_error($con));
                echo $vars->fetch_object()->UID_NUM." entries already exist";
        }
    }
    else{
        //header('location: '.$_SERVER['HTTP_REFERER'].'?e='.mysqli_error($con));
        echo $SQL_QUERY_NAME;
        echo "</br>".mysqli_error($link);
    }
    
    
}
?>