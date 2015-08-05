<?php
//error_reporting(E_ERROR);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../barcode/validate.php';

session_start();
$json_arr=array();
$artist = addslashes(filter_input(INPUT_GET,'term',FILTER_SANITIZE_STRING));
$type = addslashes(filter_input(INPUT_POST, 'type',FILTER_SANITIZE_STRING));//addslashes($_GET['type']);

if(validate_UPCABarcode($artist)||  validate_EAN13Barcode($artist)){
    $artist = substr($artist, 1,10);
    $artist = ltrim($artist,'0');
}

include_once '../../TPSBIN/functions.php';
include_once '../../TPSBIN/db_connect.php';

$query_artist = "SELECT RefCode, datein, artist, album, genre, status, recordlabel.Name as label_name FROM library LEFT JOIN recordlabel on library.labelid=recordlabel.LabelNumber where artist REGEXP '$artist' or refcode='$artist' order by soundex(artist) asc limit 50;";

$result=$mysqli->query($query_artist);
if($mysqli->error){
    die($mysqli->error);
}
echo "<table class=\"table table-condensed table-hover\"><th>#</th><th>Date-In</th><th>Artist</th><th>Album</th><th>Genre</th><th>Label Name</th><th>Status</th>";
$i=1;
while($row = $result->fetch_array(MYSQLI_ASSOC)){
    //echo $row['artist'] ."<br/>";
    //array_push($json_arr,$row['artist']);
    echo"<tr><td><button type=\"button\" onclick='edit(".$row['RefCode'].")' class=\"btn btn-default btn-xs\">".$row['RefCode']." <i class=\"fa fa-edit\" aria-hidden=\"true\"></i></button>
</td><td>".$row['datein']."</td><td>".$row['artist']."</td><td>".$row['album']."</td><td>".$row['genre']."</td><td>".$row['label_name']."</td><td>".$row['status']."</td></tr>
        ";
    $i++;
}
/*foreach (mysqli_fetch_array($result) as $row){
    echo $row['artist']."<br/>";
}*/
echo "</table>";
if( $i > 49){
    echo "<div class=\"alert alert-danger\" role=\"alert\">Results capped at 50, please refine search</div>";
}
//echo "<h3>Complete:$artist</h3>";
$result->free();
$mysqli->close();
//echo "$artist";