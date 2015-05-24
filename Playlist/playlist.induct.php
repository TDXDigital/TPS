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
$playlist = filter_input(INPUT_POST, "playlist")?:FALSE;
$print = filter_input(INPUT_POST, "print")? : 0;
$accepted = filter_input(INPUT_POST, "accepted")? :0;
$variousartists = filter_input(INPUT_POST, "va")? :0;
$label_size = filter_input(INPUT_POST, "Label_Size")? : 1;
$locale = filter_input(INPUT_POST, "locale")? :"international";
$release_date = filter_input(INPUT_POST,'rel_date')?:NULL;
$note = filter_input(INPUT_POST, "notes")?:NULL;

if($locale=="International"){
    $CanCon=0;
}
else{
    $CanCon=1;
}

if($accepted<>0){
    $accepted = 1;
}
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

if($playlist===FALSE){
    // check if entry exists in playlist table
    
    // if so, report error
    
    // else lleave set to FALSE
    $playlist=1;
}
else{
    // check if entriy exists in 'playlist' table
    
    // if rejected, it cannot go to playlist by definition.
    // set to FALSE in that case (1)
    if(!$accepted){
        $playlist = 1;
    }
    else{
        // if not set to 'PENDING'
        $playlist = 0;

        // if so set to 'COMPLETE'
        // this should no happen unless changing back to already set value??
    }
}

if(!$stmt3 = $mysqli->prepare("INSERT INTO library(datein,artist,album,variousartists,format,genre,status,labelid,Locale,CanCon,release_date,year,note,playlist_flag)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)")){
    $stmt3->close();
    header("location: ../Playlist/?q=new&e=".$stmt3->errno."&s=3&m=".$stmt3->error);
}
if(!is_null($release_date)){
    $year = date('Y',strtotime($release_date));
}
else{
    $year = NULL;
}
if(!$stmt3->bind_param(
        "sssissiisisssi",
        $datein,
        $artist,
        $album,
        $variousartists,
        $format,
        $genre,
        $accepted,
        $labelNum,
        $locale,
        $CanCon,
        $release_date,
        $year,
        $note,
        $playlist
        )){
    $stmt3->close();    
    header("location: ../Playlist/?q=new&e=".$mysqli->errno."&s=3_b&m=".$mysqli->error);
}

if(!$stmt3->execute()){
    error_log("SQL-STMT Error (SEG-3):[".$mysqli->errno."] ".$mysqli->error);
    $error = [$mysqli->errno,$mysqli->error];
    $stmt3->close();
    header("location: ../Playlist/?q=new&e=".$error[0]."&s=3&m=".$error[1]);
    //echo "ERROR #".$mysqli->errno . "  " .    $mysqli->error;
}
else{
    $id_last = $stmt3->insert_id;
    $stmt3->close();
    if($stmt4=$mysqli->prepare("INSERT INTO band_websites (ID,URL,Service) VALUES (?,?,?)")){
        $stmt4->bind_param("iss",$id_last,$url,$service);
        $services=[
            "twitter"=>filter_input(INPUT_POST, 'twitter',FILTER_SANITIZE_URL),
            "facebook"=>filter_input(INPUT_POST, 'facebook',FILTER_SANITIZE_URL),
            "bandcamp"=>filter_input(INPUT_POST, 'bandcamp',FILTER_SANITIZE_URL),
            "website"=>filter_input(INPUT_POST, 'website',FILTER_SANITIZE_URL)
        ];
        foreach($services as $key=>$value){
            $url=$value;
            $service=$key;
            if($value!=""&&!is_null($value)){
                /*if(!$stmt4->execute())
                {
                    $webresult .= $mysqli->error;
                }*/
                $stmt4->execute();
            }
        }
    }
    /*else{
        $webresult .= $mysqli->error;
    }*/
    
    if(strtolower(substr($artist,-1))!='s'){
        $s = "s";
    }
    else{
        $s="";
    }
    if($print==1){
        $_SESSION['PRINTID'][]=$id_last;
    }
    header("location: ../Playlist/?q=new&m=$artist'$s%20new%20album%20entered ($id_last)");
}
