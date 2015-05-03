<?php

function CheckUpdate($file) {
    if(strpos($file,"://")||strpos($file,"\\\\"))
    {
       die(http_response_code(403));
    }
    $Update_PKG = json_decode(file_get_contents($file),true);
    switch ($Update_PKG['type']):
        case 'database':
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
                    //file_get_contents($UPDATE_PKG['SQL_QRY']['']);
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
                            $match = []; $diff = [];
                            foreach($keys as $key=>$val){
                                array_push($match,array_intersect_key($new[$key],$keys[$key]));
                                //var_dump($keydiff);
                                array_push($diff,array_diff_assoc($keys[$key],$match[$key]));
                            }
                        }
                        else{
                            echo "NOT ARRAY: $test";
                        }
                        var_dump($match);
                        var_dump($keys);
                        var_dump($diff);
                    }
                }
                #$test = $mysqli->query()
            }
            else{
                http_response_code(400);
            }
            break;
        default :
            print 'default';
            break;
    endswitch;
}

function ApplyUpdate($param) {
    
}

error_reporting(0);
$callerIP = $_SERVER['SERVER_ADDR'];
if(!$callerIP=localhost)
{
    die(http_response_code(403));
}
$type = filter_input(INPUT_GET,'q',FILTER_SANITIZE_SPECIAL_CHARS);
$file = filter_input(INPUT_GET,'f',FILTER_SANITIZE_SPECIAL_CHARS);
if($type=='a'){
    ApplyUpdate("proc/".$file);
}
elseif($type=='c'){
    CheckUpdate("proc/".$file);
}
else{
    http_response_code(404);
}
