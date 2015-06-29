<?php
$exact=filter_input(INPUT_GET,'exact',FILTER_SANITIZE_NUMBER_INT)?:FALSE;

function GetLibraryfull($artist, $album=NULL){
    /*
     * Get all key library information based on 
     * given input and return in json format
     * all values that match the paramaters
     */
    global $mysqli,$exact;
    $result = array();
    if($artist===Null){
        $artist='%';
    }
    elseif(!$exact){
        $artist="%{$artist}%";
    }
    if($album===Null){
        $album='%';
    }
    elseif(!$exact){
        $album="%{$album}%";
    }
    if($stmt = $mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
            . "`format`,variousartists,`condition`,genre,`status`,labelid,"
            . "Locale,CanCon,updated,release_date,note,playlist_flag "
            . "FROM library where "
            . "artist like ? and album like ?")){
        $stmt->bind_param('ss',$artist,$album);
        $stmt->execute();
        $stmt->bind_result($datein,$dateout,$RefCode_q,
                $artist_q,$album_q,$format,$variousartists,
                $condition,$genre,$status,$labelid,
                $Locale,$CanCon,$updated,$release_date,
                $note,$playlist_flag);
        while($stmt->fetch()){
            array_push($result, array(
                'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                'variousartists'=>$variousartists,
                'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                'labelid'=>$labelid,
                'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                'release_date'=>$release_date,
                'note'=>$note,'playlist_flag'=>$playlist_flag
            ));
        }
        $stmt->close();
    }
    else{
        $result=["error"=>$mysqli->error];
    }
    return $result;
}

function GetLibraryRefcode($refcode){
    global $mysqli,$exact;
    $result = array();
    if($refcode===Null){
        $refcode='%';
    }
    /*elseif(!$exact){
        $refcode="%{$refcode}%";
    }*/
    if($stmt = $mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
            . "`format`,variousartists,`condition`,genre,`status`,labelid,"
            . "Locale,CanCon,updated,release_date,note,playlist_flag "
            . "FROM library where "
            . "Refcode = ?")){
        $stmt->bind_param('s',$refcode);
        $stmt->execute();
        $stmt->bind_result($datein,$dateout,$RefCode_q,
                $artist_q,$album_q,$format,$variousartists,
                $condition,$genre,$status,$labelid,
                $Locale,$CanCon,$updated,$release_date,
                $note,$playlist_flag);
        while($stmt->fetch()){
            array_push($result, array(
                'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                'variousartists'=>$variousartists,
                'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                'labelid'=>$labelid,
                'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                'release_date'=>$release_date,
                'note'=>$note,'playlist_flag'=>$playlist_flag
            ));
        }
        $stmt->close();
    }
    else{
        $result=["error"=>$mysqli->error];
    }
    return $result;
}

function ListLibrary(){
    global $mysqli;
    $result = [];
    $library = $mysqli->query(
            "SELECT RefCode,artist,album,status FROM library");
    while($result_temp = $library->fetch_array(MYSQLI_ASSOC)){
        array_push($result, $result_temp);
    }
    return $result;
}
