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

//require_once '../Update/update.php';
include_once "../TPSBIN/functions.php";

define("HOST",$_SESSION['host']);
define("PASSWORD",$_SESSION['password']);
define("USER",$_SESSION['user']);
define("DB",$_SESSION['database']);

$files = \glob("../Update/proc/*.json");
$updates = array();

function installUpdate($Update_PKG){
    if($Update_PKG['execute']=='SQL'){
        if(strtoupper($Update_PKG['SQL_QRY']['UPDATE_TYPE'])==="FILE"){
            $mysqli = new mysqli(HOST, USER, PASSWORD, DB);
            $sql_file = $Update_PKG['SQL_QRY']['UPDATE'];
            if(strtolower(substr($sql_file,-4))==='.sql'){
                if(!$sql = file_get_contents(__DIR__."/..".DIRECTORY_SEPARATOR.
                        'Update'.DIRECTORY_SEPARATOR.'proc'.DIRECTORY_SEPARATOR.$sql_file)){
                    error_log("could not open ".__DIR__."/..".DIRECTORY_SEPARATOR.
                        'Update'.DIRECTORY_SEPARATOR.'proc'.DIRECTORY_SEPARATOR.$sql_file);
                    http_response_code(400);
                    return FALSE;
                }
                $mysqli->autocommit(FALSE);
                $mysqli->begin_transaction();
                foreach(explode("||",$sql) as $query){
                    $query = preg_replace('~[\r\n]+~', ' ', $query);
                    if(!$mysqli->query($query)){
                        $error = $mysqli->error;
                        $code = $mysqli->errno;
                        $mysqli->rollback();
                        return json_encode(array("Status"=>false,
                            "Result"=>array("SQL"=>$query,
                            "ERROR"=>$error,"CODE"=>$code)));
                    }
                }
                $mysqli->commit();
                $mysqli->autocommit(TRUE);
                return json_encode(array("Status"=>true,"Result"=>array("")));
            }
        }
    }
    else{
        return http_response_code(400);
    }
}

foreach ($files as $file) {
    error_log("checking $file", $message_type=LOG_INFO);
    $string = file_get_contents($file);
    $json_a = json_decode($string, true); // The JSON contents of the update file

    $key = $json_a['TPS_Errno'];

    $placeAt = -1;
    foreach ($updates as $i=>$update)
	if (key_exists("requires", $update) && $update["requires"] == $key)
	    $placeAt = $i;

    if ($placeAt == -1)
	array_push($updates, $json_a);
    else
	array_splice($updates, $placeAt, 0, array($json_a));
}

foreach ($updates as $update) {
    error_log("Applying update {$update['TPS_Errno']}", $message_type=LOG_INFO);
    installUpdate($update, "");
}
//$mysqli->close();
print json_encode(["status"=>"Complete"]);
