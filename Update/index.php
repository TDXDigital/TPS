<?php
require_once 'update.php';
error_reporting( E_ERROR );
// function not consistent 
// http://stackoverflow.com/questions/5705082/is-serverserver-addr-safe-to-rely-on

/*$callerIP = $_SERVER['SERVER_ADDR']; 
if(!$callerIP==localhost)
{
    print json_encode(array("status"=>false,"result"=>array("ADDR"=>$callerIP)));
    die(http_response_code(403));
}*/
$type = filter_input(INPUT_GET,'q',FILTER_SANITIZE_SPECIAL_CHARS)?:
        filter_input(INPUT_POST,'q',FILTER_SANITIZE_SPECIAL_CHARS);
$file = filter_input(INPUT_GET,'f',FILTER_SANITIZE_SPECIAL_CHARS)?:
        filter_input(INPUT_POST,'f',FILTER_SANITIZE_SPECIAL_CHARS);
$path = filter_input(INPUT_GET, 'd')?:
        filter_input(INPUT_POST, 'd')?: "proc/";
if(strtolower($type)==='a'){
    ApplyUpdate($path.$file,$path);
}
elseif(strtolower($type)==='c'){
    CheckUpdate($path.$file);
}
else{
    http_response_code(404);
}
