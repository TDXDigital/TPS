<?php
include_once 'psl-config.php';   // As functions.php is not included
//echo constant("HOST");
if(!defined("HOST") || !defined("USER") || !defined("PASSWORD") || !defined("DATABASE")){
    session_destroy();
    header('location: /Security/login.php?e=invalid%20params');
}

if(!$mysqli = new mysqli(constant("HOST"), constant("USER"), constant("PASSWORD"), constant("DATABASE"))){
    //header('location: /Security/login.php?e=database%20access%20denied');
    session_destroy();
    header('location: /Security/login.php?e=database%20access%20denied');
}
?>
