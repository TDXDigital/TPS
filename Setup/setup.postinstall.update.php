<?php

/* 
 * The MIT License
 *
 * Copyright 2016 James.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once '../Update/update.php';

$files = \glob("../Update/proc/*.json");
$updates = array();

foreach ($files as $file) {
    error_log("checking $file", $message_type=LOG_INFO);
    $string = file_get_contents($file);
    $json_a = json_decode($string, true);

    $key = $json_a['TPS_Errno'];
    $insertLocationRequired = False;
    if(key_exists("requires", $json_a)){
        $insertLocationRequired = array_search($json_a['TPS_Errno'],
                array_keys($updates));
    }
    
    $keyLocation = array_search($key, array_keys($updates));
    if($keyLocation){
     #find key of first update that "requires" this update    
    }
    # insert in the earliest required position min(x,y)
    
    $inserted = array( 'x' ); // Not necessarily an array

    array_splice( $original, 3, 0, $inserted ); // splice in at position 3
    // $original is now a b c x d e
}

print json_encode(["status"=>"Complete"]);
