<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$response=[];

if($mysqli=mysqli_connect($host, $user, $password, $port)){
    /* return name of current default database */
    if ($result = $mysqli->query("SELECT DATABASE()")) {
        $row = $result->fetch_row();
        //printf("Default database is %s.\n", $row[0]);
        $response['defaultdb']=$row[0];
        $result->close();
    }
    $mysqli->select_db($database);
    if ($result = $mysqli->query("SELECT DATABASE()")) {
        $row = $result->fetch_row();
        //printf("Default database is %s.\n", $row[0]);
        if($mysqli->num_rows>0){
            $response['selectdb']=true;
        }
        else{
            $response['selectdb']=false;
        }
        $result->close();
    }
    $response['errno']=  null;
    $response['status']=true;
    $response['error']= null;
    $response['empty']=true;
    //$response['default']="null";
}
else{
    $response['errno']=  mysqli_connect_errno();
    $response['status']=false;
    $response['error']= mysqli_connect_error();
}

json_encode($response);
?>