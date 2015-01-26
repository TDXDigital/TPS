<?php
//echo constant("HOST");
if(!defined("HOST") || !defined("USER") || !defined("PASSWORD") || !defined("DATABASE")){
    session_destroy();
    echo "<br>HOST:".constant("HOST");
    echo "<br>USER:".constant("USER");
    echo "<br>PASSWORD:".constant("PASSWORD");
    echo "<br>DATABASE:".constant("DATABASE");
    echo "<br><br><a href=/Security/login.html?e=invalid%20params>Return to login</a>";
    //header('location: /Security/login.html?e=invalid%20params');
}

include_once 'psl-config.php';   // As functions.php is not included

if(!$mysqli = new mysqli(constant("HOST"), constant("USER"), constant("PASSWORD"), constant("DATABASE"))){
    //header('location: /Security/login.php?e=database%20access%20denied');
    session_destroy();
    header('location: /Security/login.php?e=database%20access%20denied');
}
?>
