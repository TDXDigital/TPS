<?php
//error_reporting(E_ERROR);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
$json_arr=array();
$artist = addslashes(filter_input(INPUT_GET,'term',FILTER_SANITIZE_STRING));
$type = addslashes(filter_input(INPUT_POST, 'type',FILTER_SANITIZE_STRING));//addslashes($_GET['type']);

include_once '../../TPSBIN/functions.php';
include_once '../../TPSBIN/db_connect.php';

$query_artist = "SELECT artist, album FROM library where artist REGEXP '$artist' group by soundex(artist) order by soundex(artist) asc LIMIT 10";

$result=$mysqli->query($query_artist);
if($mysqli->error){
    die($mysqli->error);
}
while($row = $result->fetch_array(MYSQLI_ASSOC)){
    //echo $row['artist'] ."<br/>";
    array_push($json_arr,$row['artist']);
}
/*foreach (mysqli_fetch_array($result) as $row){
    echo $row['artist']."<br/>";
}*/
echo json_encode($json_arr);
//echo "<h3>Complete:$artist</h3>";
$result->free();
$mysqli->close();
//echo "$artist";
?>