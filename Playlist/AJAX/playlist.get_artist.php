<?php
//error_reporting(E_ERROR);

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
$json_arr=array();
$value = addslashes(filter_input(INPUT_GET,'term',FILTER_SANITIZE_STRING))? :".*";
$type = addslashes(filter_input(INPUT_GET, 'type',FILTER_SANITIZE_STRING))? :"artist";
$search_method = addslashes(filter_input(INPUT_GET, 'method',FILTER_SANITIZE_STRING))?:"";

if($search_method==='any'){
    $method="REGEXP '$value'";
}
else if ($search_method==='begins'){
    $method="LIKE CONCAT('$value','%')";
}
else if ($search_method==='ends'){
    $method="LIKE CONCAT('%','$value')";
}
else if ($search_method==='exact'){
    $method="='$value'";
}
else{
    $method="REGEXP '$value'";
}

$table = "";
if($type==='artist'){
    $table='artist';
}
else if($type==='album'){
    $table='album';
}
else{
    $table='artist';
}

include_once '../../TPSBIN/functions.php';
include_once '../../TPSBIN/db_connect.php';

$query = "SELECT $table FROM library where $table $method or refcode='$value' "
        . "group by soundex($table) order by soundex($table) asc LIMIT 10";

$result=$mysqli->query($query);
if($mysqli->error){
    die($mysqli->error."<br>".$query);
}
while($row = $result->fetch_array()){
    //echo $row['artist'] ."<br/>";
    array_push($json_arr,$row[0]);
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