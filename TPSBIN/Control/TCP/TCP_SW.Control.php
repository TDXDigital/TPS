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
    elseif($_GET['q']=="EM24"){
        $out = "*0B,B,A,A,A,A,A,A,A";
    }
    elseif($_GET['q']=="lock"){
        $out = "*0CLL";
    }
    elseif($_GET['q']=="unlock"){
        $out = "*0CLU";
    }
    elseif($_GET['q']=="B1A"){
        $out = "*0B,A,B,A,A,A,A,A,A";
    }
    elseif($_GET['q']=="B2A"){
        $out = "*0B,A,A,B,A,A,A,A,A";
    }
    elseif($_GET['q']=="BBA"){
        $out = "*0B,A,B,B,A,A,A,A,A";
    }
    else{
        die("Unknown Command");
    }
    fwrite($fp, $out);
    stream_set_timeout($fp,4,0);
    //$res = fread($fp,8192);
    //$res = stream_get_contents($fp);
    
    //while (!feof($fp)) {
      $res .= fread($fp, 8192);
    //}
    $info = stream_get_meta_data($fp);
    fclose($fp);
    
    if ($info['timed_out']) {
        echo 'Connection timed out!';
    } else {
        echo $res;
    }
}
?>