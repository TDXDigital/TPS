<?php
error_reporting(0);
if($stream = file_get_contents('http://172.22.10.13:8000/currentsong?sid=2')){
    echo $stream;
}
else{
    http_response_code(404);
    echo "error";
}


