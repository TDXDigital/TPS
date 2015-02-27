<?php

/* 
 * Translates information into database.
 */

include_once '../TPSBIN/function.php';
include_once '../TPSBIN/db_connect.php';

$artist = filter_input(INPUT_POST, "artist");
$album = filter_input(INPUT_POST,"album");
$genre = filter_input(INPUT_POST,"genre");
$datein = filter_input(INPUT_POST, "indate");
$label = filter_input(INPUT_POST, "label");
$format = filter_input(INPUT_POST, "format");
$print = filter_input(INPUT_POST, "print")? : 0;
$accepted = filter_input(INPUT_GET, "accepted")? :0;