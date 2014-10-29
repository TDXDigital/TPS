<?php
include_once 'psl-config.php';   // As functions.php is not included
if(!$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE)){
    header('location:/Security/login.php?e=database%20access%20denied');
}
?>