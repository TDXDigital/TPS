<?php
function DatabaseUpdateCheck($Update_PKG){
    if($Update_PKG['execute']=='SQL'){
        if(!defined($mysqli)){
            if(!include_once '../TPSBIN/functions.php'){
                printf("Exception");
                throw new Exception("database connection failed - file not found");
            }
            sec_session_start();
            if(!include_once '../TPSBIN/db_connect.php'){
                printf("Exception");
                throw new Exception("database connection failed - file not found");
            }
        }
        if($mysqli->connect_errno){
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        if($Update_PKG["SQL_QRY"]['TEST']!=''){
            $sql_simple = [];
            if($res = $mysqli->query($Update_PKG["SQL_QRY"]['TEST'])){
                $result = [];
                $key_only = []; // likely not needed
                $keys = $Update_PKG["SQL_QRY"]['RESULT'];
                $Z = [];
                foreach ($keys as $key => $val){
                    array_push($key_only,$key);
                    $r = 0;
                    foreach ($val as $data){
                        array_push($result,array($key=>$data));
                        // insert additional matching keys in some way. not sure yet $result[$r] should work but is not...
                        $r++;
                    }

                }
                $test = [];
                for($i=0;$i!=$res->num_rows;$i++){
                    array_push($test,$res->fetch_array(MYSQLI_ASSOC));
                }
                $new = array();
                foreach($test as $key => $value) 
                {
                  foreach ($value as $num_key => $content)
                  {
                    $new[$num_key][$key] = $content;
                  }
                }

                /*var_dump($new);
                var_dump($keys);*/

                //add match levels
                //$test = $res->fetch_object();//array(MYSQLI_ASSOC);
                if(is_array($Update_PKG["SQL_QRY"]['RESULT'])||sizeof($test)>1){
                    /*foreach ($key_only as $key_i){
                        $diff = array_diff($result,$test);
                    }*/
                    $match = []; $diff = []; $return = []; $klist = [];
                    $f = 0;
                    foreach($keys as $key=>$val){
                        $dt = [];
                        $temp[$f]=array_intersect_key($new,$keys);
                        foreach($temp[$f] as $gar=>$v2){
                            $match[$gar]=$v2;
                        }
                        //$submatch = array_push($match[$key]);
                        //var_dump($match);
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
                    //echo "NOT ARRAY: $test";
                    http_response_code(400);
                }
                $Pass = FALSE;
                if(empty($return)&&sizeof($key_only)===sizeof($match)){
                    // assume diff was fine
                    $Pass = TRUE;
                }
                else{
                    // FAIL, due to empty string or mismatch size
                    //echo sizeof($key_only)." : ".sizeof($match);
                }
                $final = array("Status"=>$Pass,"Result"=>$return);
                /*echo sizeof($key_only);
                echo sizeof($new);*/
                return json_encode($final);
                /*var_dump($match);
                var_dump($keys);
                var_dump($diff);*/
            }
        }
    }
    else{
        http_response_code(400);
    }
}

function DatabaseUpdateApply($Update_PKG,$path){
    if($Update_PKG['execute']=='SQL'){
        
        //error_log("SQL Selected");
        if(!defined($mysqli)){
            if(!include_once '../TPSBIN/functions.php'){
                printf("Exception");
                throw new Exception("database connection failed - file not found");
            }
            sec_session_start();
            if(!include_once '../TPSBIN/db_connect.php'){
                printf("Exception");
                throw new Exception("database connection failed - file not found");
            }
        }
        //error_log("skipping check");
        //error_log("checking if update is required");
        //$needed = json_decode(DatabaseUpdateCheck($Update_PKG));
        //error_log("JSON:".json_encode($needed));
        //if($needed['Status']===false){
            // needs update
            if(strtoupper($Update_PKG['SQL_QRY']['UPDATE_TYPE'])==="FILE"){
                $sql_file = $Update_PKG['SQL_QRY']['UPDATE'];
                if(strtolower(substr($sql_file,-4))==='.sql'){
                    // load SQL file
                    if(!$sql = file_get_contents($path.$sql_file)){
                        http_response_code(400);
                    }
                    else{
                        if(!$mysqli->query($sql)){
                            //http_response_code(400);
                            return json_encode(array("Status"=>false,"Result"=>array("SQL"=>$sql,"ERROR"=>$mysqli->errno)));
                        }
                        else{
                            return json_encode(array("Status"=>true,"Result"=>array("")));
                        }
                    }
                }
            }
        /*}
        elseif($needed['Status']===null){
            http_response_code(400);
        }*/
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
            http_response_code(400);//json_encode(array("Status"=>null,"Result"=>array("ERROR")));
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
            http_response_code(400);//json_encode(array("Status"=>null,"Result"=>array("ERROR")));
            break;
    endswitch;
}

error_reporting(0);
$callerIP = $_SERVER['SERVER_ADDR'];
if(!$callerIP=localhost)
{
    die(http_response_code(403));
}
$type = filter_input(INPUT_GET,'q',FILTER_SANITIZE_SPECIAL_CHARS)?:
        filter_input(INPUT_POST,'q',FILTER_SANITIZE_SPECIAL_CHARS);
$file = filter_input(INPUT_GET,'f',FILTER_SANITIZE_SPECIAL_CHARS)?:
        filter_input(INPUT_POST,'f',FILTER_SANITIZE_SPECIAL_CHARS);
$path = filter_input(INPUT_GET, 'd')?:
        filter_input(INPUT_GET, 'd')?: "proc/";
if(strtolower($type)==='a'){
    ApplyUpdate($path.$file,$path);
}
elseif(strtolower($type)==='c'){
    CheckUpdate($path.$file);
}
else{
    http_response_code(404);
}
