<?php
    session_start();
    if(!isset($_SESSION['access'])){
        error_log("UNAUTHORIZED SESSION CHANGE ATTEMPT: No Session Set, Returned 403 (".$_SERVER['REMOTE_ADDR'].")");
        die(
            header('HTTP/1.1 403 Forbidden')
        );
    }
    else{
        $val = $_GET['v'];
        $cmd = $_GET['c'];
        switch ($cmd){
                case "SetHardwareOff" : 
                $_SESSION['hardware_prompt'] = "FALSE";
                header('HTTP/1.1 200 OK');
            break;
                case "SetHardwareOn" :
                $_SESSION['hardware_prompt'] = "TRUE";
                header('HTTP/1.1 200 OK');
            break;
            default:
                error_log("UNAUTHORIZED SESSION CHANGE ATTEMPT: Invalid Command, Returned 403 (".$_SERVER['REMOTE_ADDR'].")");
                die(
                    header('HTTP/1.1 403 Forbidden')
                );
            break;
        } 
    }
?>