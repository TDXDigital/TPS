<?php
error_reporting(0);
$config = dirname(dirname(dirname(dirname(dirname(
        __FILE__))))).DIRECTORY_SEPARATOR."CONFIG.php";
$streamServer = $streamServer?:"http://172.22.10.13:8000";
$streamSid= $streamSid?"sid=?".$streamSid:"sid=2";
$ctx = stream_context_create(array(
    'http' => array(
        'timeout' => 0.33
        )
    )
);
// no response in 0.33s, cancel call
if($stream = file_get_contents("$streamServer/currentsong?sid=$streamSid",
        0, $ctx)){
    echo $stream;
}
else{
    http_response_code(400);
    echo "error";
}


