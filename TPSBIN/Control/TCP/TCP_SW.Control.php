<?php
$fp = fsockopen("ckxu3400lg.local.ckxu.com", 23, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    if($_GET['q']=="POLL"){
        $out = "*POLL";
    }
    elseif($_GET['q']=="0SL"){
        $out = "*0SL";
    }
    elseif($_GET['q']=="0U"){
        $out = "*0Y";
    }
    else{
        die("Unknown Command");
    }
    fwrite($fp, $out);
    stream_set_timeout($fp,3);
    $res = fread($fp,2000);
    $info = stream_get_meta_data($fp);

    fclose($fp);
    
    if ($info['timed_out']) {
        echo 'Connection timed out!';
    } else {
        echo $res;
    }
}
?>