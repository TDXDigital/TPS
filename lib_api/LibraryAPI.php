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

function SearchLibrary($term){
    throw new Exception('Deprecated, use library');
    /*
     * Get all key library information based on 
     * given input and return in json format
     * all values that match the paramaters
     */
    global $mysqli,$exact;
    $result = array();
    if(!$exact){
        $term="%{$term}%";
    }
    if($stmt = $mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
            . "`format`,variousartists,`condition`,genre,`status`,labelid,"
            . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
            . "FROM library where "
            . "artist like ? or album like ? or note like ? or"
            . " Locale like ? or genre like ?")){
        $stmt->bind_param('sssss',$term,$term,$term,$term,$term);
        $stmt->execute();
        $stmt->bind_result($datein,$dateout,$RefCode_q,
                $artist_q,$album_q,$format,$variousartists,
                $condition,$genre,$status,$labelid,
                $Locale,$CanCon,$updated,$release_date,
                $note,$playlist_flag,$year);
        while($stmt->fetch()){
            array_push($result, array(
                'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                'variousartists'=>$variousartists,
                'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                'labelid'=>$labelid,
                'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                'release_date'=>$release_date,
                'note'=>$note,'playlist_flag'=>$playlist_flag,'year'=>$year,
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
    if($stmt = $mysqli->prepare("SELECT Barcode,year,datein,dateout,RefCode,artist,album,"
            . "`format`,variousartists,`condition`,genre,`status`,labelid,"
            . "Locale,CanCon,updated,release_date,note,playlist_flag,governmentCategory,"
            . "scheduleCode "
            . "FROM library where "
            . "Refcode = ?")){
        $stmt->bind_param('s',$refcode);
        $stmt->execute();
        $stmt->bind_result($barcode,$year,$datein,$dateout,$RefCode_q,
                $artist_q,$album_q,$format,$variousartists,
                $condition,$genre,$status,$labelid,
                $Locale,$CanCon,$updated,$release_date,
                $note,$playlist_flag,$govCat,$scCode);
        while($stmt->fetch()){
            array_push($result, array(
                'barcode'=>$barcode,'year'=>$year,
                'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                'variousartists'=>$variousartists,
                'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                'labelid'=>$labelid,
                'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                'release_date'=>$release_date,
                'note'=>$note,'playlist_flag'=>$playlist_flag,
                'governmentCategory'=>$govCat,'scheduleCode'=>$scCode,
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
    if(is_null($mysqli)){
        return '';#$mysqli = $GLOBALS['db'];
    }
    $result = [];
    $library = $mysqli->query(
            "SELECT RefCode,artist,album,status FROM library");
    while($result_temp = $library->fetch_array(MYSQLI_ASSOC)){
        array_push($result, $result_temp);
    }
    return $result;
}

function GetLabelbyId($labelid){
    throw new Exception('Function Deprecated, Use new library');
    global $mysqli;
    $result = array();
    /*elseif(!$exact){
        $refcode="%{$refcode}%";
    }*/
    if($stmt = $mysqli->prepare("SELECT LabelNumber, Name, Location, Size,"
            . "name_alias_duplicate as alias, updated, verified FROM recordlabel"
            . " WHERE LabelNumber=?")){
        $stmt->bind_param('i',$labelid);
        $stmt->execute();
        $stmt->bind_result($LabelNumber,$name,$location,$size,$alias,$updated,
                $verified);
        while($stmt->fetch()){
            array_push($result, array(
                'labelNumber'=>$LabelNumber,'name'=>$name,'location'=>$location,
                'size'=>$size,'alias'=>$alias,'updated'=>$updated,'verified'=>$verified,
            ));
        }
        $stmt->close();
    }
    else{
        $result=["error"=>$mysqli->error];
    }
    return $result;
}
function GetWebsitesbyRefCode($id){
    throw new Exception('Fucntion Deprecated Use new library');
    global $mysqli;
    $result = array();
    /*elseif(!$exact){
        $refcode="%{$refcode}%";
    }*/
    if($stmt = $mysqli->prepare("SELECT ID, URL, Service, date_available as startDate,"
            . "date_discontinue as endDate FROM band_websites"
            . " WHERE ID=?")){
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $stmt->bind_result($id,$url,$service,$available,$end);
        while($stmt->fetch()){
            $result[$service]=array(
                'id'=>$id,
                'url'=>$url,
                'active'=>$available,
                'discontinued'=>$end,
            );
        }
        $stmt->close();
    }
    else{
        $result=["error"=>$mysqli->error];
    }
    return $result;
}
