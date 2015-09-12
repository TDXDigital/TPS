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

//  GET VALUES
//$ENAB = addslashes($_POST['Enabled']);
if(isset($_POST['enabled'])){
    $ENAB = '1';
}
else{
    $ENAB = '0';
}
//$RQAR = addslashes($_POST['RQArtist']);
if(isset($_POST['Artist'])){
    $RQAR = '1';
}
else{
    $RQAR = '0';
}
//$RQAL = addslashes($_POST['RQAlbum']);
if(isset($_POST['Album'])){
    $RQAL = '1';
}
else{
    $RQAL = '0';
}
//$RQHR = addslashes($_POST['RQAfterHr']);
if(isset($_POST['RQAfterHr'])){
    $RQHR = '1';
}
else{
    $RQHR = '0';
}
//$RQCO = addslashes($_POST['RQComposer']);
if(isset($_POST['Composer'])){
    $RQCO = '1';
}
else{
    $RQCO = '0';
}
//$SHPR = addslashes($_POST['ShowPrompt']);
if(isset($_POST['prompt'])){
    $SHPR = '1';
}
else{
    $SHPR = '0';
}

// GET DATES
$STAR = addslashes($_POST['start']);
$ENDD = addslashes($_POST['end']);

// GET STATION
$STNI = addslashes($_POST['station']);

// GET DESCRIPTION
$DESC = addslashes(htmlspecialchars($_POST['description']));


$SQL_INSERT = "INSERT INTO socan (Enabled,RQArtist,RQAlbum,RQComposer,start,end,RQAfterHr,ShowPrompt,StationID,Description)
 VALUES ('$ENAB','$RQAR','$RQAL','$RQCO','$STAR','$ENDD','$RQHR','$SHPR','$STNI','$DESC')";
//$SQL_QUERY_NAME = "SELECT UID, count(uid) AS UID_NUM FROM genre WHERE `genreid`='$NAME'";

$link = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if(!$link){
    header('location: '.$_SERVER["HTTP_REFERER"].'?e=Database%20connection%20error');
}
else{
    //if($vars = mysqli_query($link,$SQL_QUERY_NAME)){
        //if($vars->fetch_object()->UID_NUM==0){
            if(mysqli_query($link,$SQL_INSERT)){
                header('location: ./?r=Successfully%20Added');
            }
            else{
                //header('location: ./?e=Invalid%20Parameters');
                echo $SQL_INSERT;
                echo "</br>".mysqli_error($link);
            }
        /*}
        else{
            header('location:genre.php?e=Already%20Exists');
            //echo $vars->fetch_object()->UID_NUM." entries already exist";
        }
    }
    else{
        //header('location: '.$_SERVER['HTTP_REFERER'].'?e='.mysqli_error($con));
        echo $SQL_QUERY_NAME;
        echo "</br>".mysqli_error($link);
    }*/
    
    
}
?>