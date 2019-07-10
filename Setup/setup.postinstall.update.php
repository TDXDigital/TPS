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

$packages = [];
foreach ($files as $file) {
    error_log($file,  $message_type=LOG_INFO);
    $string = file_get_contents($file);
    $updatePackage = json_decode($string, true);
    array_push($packages, $updatePackage);
}

function requires($a, $b) {
    global $packages;
    if (key_exists("requires", $a)) {
	if ($a["requires"] == $b["TPS_Errno"]) {
	    return TRUE;
	} else {
	    // Get the package that $a requires
	    foreach ($packages as $package)
		if ($a["requires"] == $package["TPS_Errno"])
		    // Make a recursive call
	            return requires($package, $b);
	    return FALSE;
	}
    } else {
	return FALSE;
    }
}

// Sort the updates based requirements
// For example, if A requires B, place B before A.
foreach ($packages as $package) {
    $key = $package['TPS_Errno'];
    $placeAt = -1;
    foreach ($updates as $i=>$update) {
	if (requires($update, $package)) {
	    $placeAt = $i;
	    break;
	}
    }

    if ($placeAt == -1)
	array_push($updates, $package);
    else
	array_splice($updates, $placeAt, 0, array($package));
}

foreach ($updates as $i=>$update) {
    error_log("Updating: {$update['TPS_Errno']}", $message_type=LOG_INFO);
    installUpdate($update, "");
}

print json_encode(["status"=>"Complete"]);
