<?php
$stream = file_get_contents('http://hyperstream:8000/currentsong?sid=2');
echo $stream;
?>
