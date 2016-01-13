<?php
error_reporting(0);
$ctx = stream_context_create(array( 
    'http' => array( 
        'timeout' => 0.33
        ) 
    ) 
); 
// no response in 0.33s, cancel call
if($stream = file_get_contents('http://172.22.10.13:8000/currentsong?sid=2',
        0, $ctx)){
    echo $stream;
}
else{
    http_response_code(404);
    echo "error";
}


