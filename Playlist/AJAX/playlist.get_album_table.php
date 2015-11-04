<?php
//error_reporting(E_ERROR);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../barcode/validate.php';

session_start();
$artist = addslashes(filter_input(INPUT_GET,'term',FILTER_SANITIZE_STRING))?:"";
$format = addslashes(filter_input(INPUT_GET, 'format',FILTER_SANITIZE_STRING))?:"html";
$limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT)?:15;

if(validate_UPCABarcode($artist)||  validate_EAN13Barcode($artist)){
    $artist = substr($artist, 1,10);
    $artist = ltrim($artist,'0');
}

include_once '../../TPSBIN/functions.php';
include_once '../../TPSBIN/db_connect.php';

$con = $mysqli->prepare("SELECT RefCode, datein, artist, album, genre, status, recordlabel.Name as label_name FROM library LEFT JOIN recordlabel on library.labelid=recordlabel.LabelNumber where artist REGEXP ? or refcode=? or barcode=? order by soundex(artist) asc limit ?;");
$result = array();
$refcodeDB = NULL;
$dateinDB = NULL;
$artistDB = NULL;
$albumDB = NULL;
$genreDB = NULL;
$labelDB = NULL;
$statusDB = NULL;

if($con){
    $con->bind_param("sssi",$artist,$artist,$artist,$limit);
    $con->bind_result($refcodeDB,$dateinDB,$artistDB,$albumDB,$genreDB,$statusDB,$labelDB);
    $con->execute();
    while($con->fetch()){
        switch ($statusDB) {
            case 0:
                $statusDB = "Rejected";
                break;
            case 1:
                $statusDB = "Accepted";
                break;
            case NULL:
                break;
            default:
                $statusDB = "Code ".$statusDB;
                break;
        }
       $result[$refcodeDB] = array(
            "datein" => $dateinDB,
            "artist" => $artistDB,
            "album" => $albumDB,
            "genre" => $genreDB,
            "status" => $statusDB,
            "labelName" => $labelDB,
            );
    }
}
else{
    $trace = debug_backtrace();
    trigger_error(
    'error via prepare(): ' . $$mysqli->error .
    ' in ' . $trace[0]['file'] .
    ' on line ' . $trace[0]['line'],
    E_USER_ERROR);
    http_response_code($response_code=500);
}

if(strtolower($format)=="json"){
    print json_encode($result);
}
else{
    echo "<table class=\"table table-condensed table-hover\"><th>#</th><th>Date-In</th><th>Artist</th><th>Album</th><th>Genre</th><th>Label Name</th><th>Status</th>";
    $i=1;
    foreach ($result as $refCode => $row) {
        echo"<tr><td><button type=\"button\" onclick='edit(".$refCode.")' class=\"btn btn-default btn-xs\">".$refCode." <i class=\"fa fa-edit\" aria-hidden=\"true\"></i></button>
            </td><td>".$row['datein']."</td><td>".$row['artist']."</td><td>".$row['album']."</td><td>".$row['genre']."</td><td>".$row['labelName']."</td><td>".$row['status']."</td></tr>
        ";
        $i++;
        if( $i > $limit-1){
            echo "<div class=\"alert alert-danger\" role=\"alert\">Results capped at 50, please refine search</div>";
        }
    }
    echo "</table>";
}
