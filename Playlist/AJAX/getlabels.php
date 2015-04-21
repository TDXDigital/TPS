<?php
error_reporting(E_ERROR);
include_once "../../TPSBIN/functions.php";
include_once '../../TPSBIN/db_connect.php';

$output = array();
$target = filter_input(INPUT_GET,'term',FILTER_SANITIZE_STRING)? : "";
$limit = filter_input(INPUT_GET,'limit',FILTER_SANITIZE_NUMBER_INT)? :5;

$result = $mysqli->prepare("SELECT recordlabel.name, LabelNumber as ID, count(*) as submissions from library join recordlabel on LabelNumber=labelid where name like concat(\"%\", ? ,\"%\") group by labelid order by submissions desc limit ? ");
$result->bind_param("si",$target,$limit);
$result->execute();
$result->bind_result($name,$ID,$occurance);
while ($result->fetch()) {
    //$output.="<option value=\"".$ID."\" >".$name."</option>";
    $output[] = $name;
}
echo json_encode($output);
