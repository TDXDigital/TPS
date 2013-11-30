<?php
session_start();
$json_arr=array();
$artist = addslashes($_GET['term']);
//$title = $_GET['T'];
$link = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']) or die("Connection Error");
$query_artist = "SELECT artist, album FROM song where Category not like '5%' and Category NOT LIKE '4%' and Category NOT LIKE '1%' and Artist REGEXP '$artist' group by soundex(artist) LIMIT 10";
$result=mysqli_query($link,$query_artist);
if(mysqli_error($link)){
    echo mysqli_error($link);
}
while($row = mysqli_fetch_array($result)){
    //echo $row['artist'] ."<br/>";
    array_push($json_arr,$row['artist']);
}
/*foreach (mysqli_fetch_array($result) as $row){
    echo $row['artist']."<br/>";
}*/
echo json_encode($json_arr);
//echo "<h3>Complete:$artist</h3>";
$result->free();
$link->close();
//echo "$artist";
?>