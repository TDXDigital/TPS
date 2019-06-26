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

function DatabaseUpdateCheck($Update_PKG){
    if($Update_PKG['execute']=='SQL'){
        if(!defined($mysqli)){
            if(!include_once '../TPSBIN/functions.php'){
                printf("Exception");
                throw new Exception(
                        "database connection failed - file not found");
            }
            sec_session_start();
            if(!include_once '../TPSBIN/db_connect.php'){
                printf("Exception");
                throw new Exception(
                        "database connection failed - file not found");
            }
        }
        if($mysqli->connect_errno){
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        if($Update_PKG["SQL_QRY"]['TEST']!=''){
            $sql_simple = [];
            if($res = $mysqli->query($Update_PKG["SQL_QRY"]['TEST'])){
                $result = []; // A list of arrays which each contain 1 key-value pair of each RESULT key-value combo
                $key_only = []; // An array of keys in RESULT
                $keys = $Update_PKG["SQL_QRY"]['RESULT'];
                $negate = $Update_PKG["SQL_QRY"]['Negate']?:0;
                $Z = [];

		// Fill $key_only and $result...
		// 1 - Loop through each key of RESULT
                foreach ($keys as $key => $val){
		    // 2 - Push the key onto $key_only
                    array_push($key_only,$key);
		    // 3 - Loop through the array values of each RESULT key
                    foreach ($val as $data){
			// 4 - Push an array onto $result of key-value pairs for each of the RESULT key values
                        array_push($result,array($key=>$data));
                    }

                }

		// Gather the SQL test query rows
                $test = []; // An array of each row from the SQL Test query -- [{"Column 1 Name":"Value","Column 2 Name":"Value"}, ...]
                for($i=0;$i!=$res->num_rows;$i++){
                    array_push($test,$res->fetch_array(MYSQLI_ASSOC));
                }

                $new = array(); // Reorganize $test to remove duplicate table titles -- [{"Column 1 Name" : ["Row 1 Value", "Row 2 Value"], "Column 2 Name" : [...], ...}]
                foreach($test as $key => $value) 
                {
                  foreach ($value as $num_key => $content)
                  {
                    $new[$num_key][$key] = $content;
                  }
                }
                if(is_array($Update_PKG["SQL_QRY"]['RESULT'])||sizeof($test)>1){
                    $match = [];
		    $diff = [];
		    $return = [];
		    $klist = [];
		    $db_drop = FALSE;
                    $f = 0; // Counter of the key you're on
                    foreach($keys as $key=>$val){
                        $dt = [];
                        $temp[$f]=array_intersect_key($new,$keys);
                        foreach($temp[$f] as $gar=>$v2){
                            $match[$gar]=$v2;
                        }
			if (empty($val))
			    $db_drop = TRUE;
                        $dt = array_diff_assoc($keys[$key],$match[$key]);

                        if(!empty($dt)){
                            array_push($diff,$dt);
                            array_push($klist,$key);
                            $return[$key]=[];
                        }
                        $f++;
                    }
                    $e=0;
                    foreach($diff as $t3){
                        $return[$klist[$e]]=$t3;
                        $e++;
                    }
                }
                else{
                    http_response_code(400);
                }
                if($negate){
                    $Pass = TRUE;
                }
                else{
                    $Pass = FALSE;
                }
                if(empty($return)&&sizeof($key_only)===sizeof($match) || $db_drop){
                    $Pass = TRUE;
		    if ($db_drop && !empty($new))
			$Pass = FALSE;
                }
                else{
                    error_log("FAIL, due to empty string or mismatch size (".
                            sizeof($key_only)." : ".sizeof($match).")");
                    if(sizeof($match)>0){
                        http_response_code(500);
                    }
                    $Pass = FALSE;
                }
                $final = array("Status"=>$Pass,"Result"=>$return);
                return json_encode($final);
            }
            else{
                if(isset($Update_PKG['SQL_QRY']['createMode'])&&
                        $Update_PKG['SQL_QRY']['createMode']==1){
                    return json_encode(array("Status"=>False,
                        "Result"=>array()));
                }
                else{
                    http_response_code(500);
                    return json_encode(array("Status"=>FALSE,
                        "Result"=>array($mysqli->errno,$mysqli->error)));
                }
            }
        }
    }
    else{
        http_response_code(400);
    }
}

function DatabaseUpdateApply($Update_PKG,$path){
    if($Update_PKG['execute']=='SQL'){
        if(!defined($mysqli)){
            if(!include_once '../TPSBIN/functions.php'){
                printf("Exception");
                throw new Exception(
                        "database connection failed - file not found");
            }
            sec_session_start();
            if(!include_once '../TPSBIN/db_connect.php'){
                printf("Exception");
                throw new Exception(
                        "database connection failed - file not found");
            }
        }
        if(strtoupper($Update_PKG['SQL_QRY']['UPDATE_TYPE'])==="FILE"){
            $sql_file = $Update_PKG['SQL_QRY']['UPDATE'];
            if(strtolower(substr($sql_file,-4))==='.sql'){
                if(!$sql = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.
                        'proc'.DIRECTORY_SEPARATOR.$sql_file)){
                    error_log("could not open ".__DIR__.DIRECTORY_SEPARATOR.
                        'proc'.DIRECTORY_SEPARATOR.$sql_file);
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

function CheckUpdate($file) {
    if(strpos($file,"://")||strpos($file,"\\\\"))
    {
       die(http_response_code(403));
    }
    $Update_PKG = json_decode(file_get_contents($file),true);
    switch ($Update_PKG['type']):
        case 'database':
            print DatabaseUpdateCheck($Update_PKG);
            break;
        default :
            http_response_code(400);
            break;
    endswitch;
}

function ApplyUpdate($file,$path) {
    if(strpos($file,"://")||strpos($file,"\\\\"))
    {
       die(http_response_code(403));
    }
    $Update_PKG = json_decode(file_get_contents($file),true);
    switch ($Update_PKG['type']):
        case 'database':
            error_log("Apply Update: database determined");
            print DatabaseUpdateApply($Update_PKG,$path);
            break;
        default :
            http_response_code(400);
            json_encode(array("Status"=>null,"Result"=>array("ERROR")));
            break;
    endswitch;
}
