<?php
//error_reporting(0);
require '../TPSBIN/Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require '../TPSBIN/functions.php';
require '../TPSBIN/db_connect.php';
$exact=filter_input(INPUT_GET,'exact',FILTER_SANITIZE_NUMBER_INT)?:FALSE;
//$db = $mysqli;

function GetLibraryfull($artist, $album=NULL){
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
        $stmt->bind_result($res['datein'],$res['dateout'],$res['RefCode'],
                $res['artist'],$res['album'],$res['format'],$res['variousartists'],
                $res['condition'],$res['genre'],$res['status'],$res['labelid'],
                $res['Locale'],$res['CanCon'],$res['updated'],$res['release_date'],
                $res['note'],$res['playlist_flag']);
        while($stmt->fetch()){
            array_push($result, $res);
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
    elseif(!$exact){
        $refcode="%{$refcode}%";
    }
    if($stmt = $mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
            . "`format`,variousartists,`condition`,genre,`status`,labelid,"
            . "Locale,CanCon,updated,release_date,note,playlist_flag "
            . "FROM library where "
            . "Refcode = ?")){
        $stmt->bind_param('s',$refcode);
        $stmt->execute();
        $stmt->bind_result($res['datein'],$res['dateout'],$res['RefCode'],
                $res['artist'],$res['album'],$res['format'],$res['variousartists'],
                $res['condition'],$res['genre'],$res['status'],$res['labelid'],
                $res['Locale'],$res['CanCon'],$res['updated'],$res['release_date'],
                $res['note'],$res['playlist_flag']);
        while($stmt->fetch()){
            array_push($result, $res);
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
    $library = $mysqli->query(
            "SELECT RefCode,artist,album,status FROM library");
    $result = $library->fetch_array(MYSQLI_ASSOC);
    return $result;
}
$app = new \Slim\Slim(array(
    'debug' => true
));
$app->get('/library/:refcode', function ($refcode) {
    print json_encode(GetLibraryRefcode($refcode));
});
$app->get('/library/artist/:artist', function ($artist) {
    print json_encode(GetLibraryfull($artist));
});
$app->get('/library/:artist/:album', function ($artist,$album) {
    print json_encode(GetLibraryfull($artist,$album));
});
$app->get('/library/', function () {
    print json_encode(ListLibrary());
});
$app->run();
