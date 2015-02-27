<?php

/* 
 * Translates information into database.
 */

include_once '../TPSBIN/functions.php';
include_once '../TPSBIN/db_connect.php';

/* @var $artist Contains the artist name */
$artist = filter_input(INPUT_POST, "artist");
$album = filter_input(INPUT_POST,"album");
$genre = filter_input(INPUT_POST,"genre")?:NULL;
$datein = filter_input(INPUT_POST, "indate")?:NULL;
$label = filter_input(INPUT_POST, "label")?:NULL;
$format = filter_input(INPUT_POST, "format")?:NULL;
$print = filter_input(INPUT_POST, "print")? : 0;
$accepted = filter_input(INPUT_POST, "accepted")? :0;
$variousartists = filter_input(INPUT_POST, "va")? :0;
$label_size = filter_input(INPUT_POST, "Label_Size")? : 1;
$locale = filter_input(INPUT_POST, "locale")? :"international";
$labelNum = NULL;

// Get label number if exists
$stmt1 = $mysqli->prepare("SELECT labelNumber FROM recordlabel where Name=? limit 1");
$stmt1->bind_param("s",$label);
if(!$stmt1->execute()){
    $stmt1->close();
    header("location: ../Playlist/?q=new&e=".$mysqli->errno."&s=1");
}
$stmt1->bind_result($labelNum);
$stmt1->fetch();
$stmt1->close();

//if does not exist create label
if(is_null($labelNum)){
    $stmt2 = $mysqli->prepare("INSERT INTO recordlabel(Name,size) VALUES (?,?)");
    $stmt2->bind_param("si",$label,$label_size);
    if(!$stmt2->execute()){
        $stmt2->close();
        header("location: ../Playlist/?q=new&e=".$mysqli->errno."&s=2");
        //echo "ERROR: " .    $mysqli->error;
    }
    else{
        $labelNum=$stmt2->insert_id;
        //echo "created recordlabel #".$labelNum;
    }
    $stmt2->close();
}
else{
    //echo $labelNum ? : " NULL ";
}
//echo "creating album...";
if($genre=="null"){
    $genre=NULL;
}
if(is_null($labelNum)||$labelNul=="null"){
    header("location: ../Playlist/?q=new&e=9999&s=3");
}

$stmt3 = $mysqli->prepare("INSERT INTO library(datein,artist,album,variousartists,format,genre,status,labelid,Locale)
    VALUES (?,?,?,?,?,?,?,?,?)");
$stmt3->bind_param("sssissiis",$datein,$artist,$album,$variousartists,$format,$genre,$status,$labelNum,$locale);
if(!$stmt3->execute()){
    $stmt3->close();
    header("location: ../Playlist/?q=new&e=".$mysqli->errno."&s=3&m=".$mysqli->error);
    //echo "ERROR #".$mysqli->errno . "  " .    $mysqli->error;
}
else{
    $id_last = $stmt3->insert_id;
    $stmt3->close();
    if(strtolower(substr($artist,-1))!='s'){
        $s = "s";
    }
    else{
        $s="";
    }
    header("location: ../Playlist/?q=new&m=$artist'$s%20new%20album%20entered ($id_last)");
}
