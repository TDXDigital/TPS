<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(0);
if(!extension_loaded('mysqli')||!extension_loaded('PDO_MySQL')){
    die(http_response_code(500));
}

$CHECKDB=false;

$return=[];
include_once "../TPSBIN/functions.php";
    if(!isset($_SESSION)){
        sec_session_start();
    }
    if(!isset($_SESSION['host'])){
        http_response_code(400);
        die("Missing critical values, please restart setup");
    }

    define("HOST",$_SESSION['host']);
    define("PASSWORD",$_SESSION['password']);
    define("USER",$_SESSION['user']);
    
    
    /*
     * Connect to DB, Do not define DATABASE
     * Could not use db_connect as DATABASE is needed.
     */
    !$mysqli = new mysqli(HOST, USER, PASSWORD);
if($mysqli->connect_error){
    /*
     * return 403
     * cancel run (exit)
     */
    http_response_code(403);
    //\header('HTTP/1.1 403 Access Denied', true, 403);
    //exit;
    
    $return=["status"=>"403","Result"=>$mysqli->connect_error,"e-code"=>$mysqli->connect_errno];
}
elseif(!isset($_SESSION['database'])){
    /*
     * cannot proceede return 400
     * exit run
     */
    //\header('HTTP/1.1 400 Bad Request', true, 400);
    http_response_code(409);
    //print "Cannot proceede, Database not set";
    $return=["status"=>"400","Result"=>$mysqli->connect_error,"e-code"=>$mysqli->connect_errno];
    //exit;
}
/*
 * Connection Established.
 */
elseif (!isset($_SESSION['callsign'])) {
    $return=["status"=>"412","Result"=>"Missing callsign, cannot continue","e-code"=>412];
}
else{
    if($mysqli->select_db($_SESSION['database'])){
        if($CHECKDB){
            http_response_code(409);
            $return=["status"=>"Error","Result"=>"Database Exists, cannot continue"];
        }
    }
    $sql = \file_get_contents("setup.postinstall.permissions.sql");
    $permissions = \file_get_contents("setup.postinstall.user.sql");
    //$sql = preg_replace("/[\\n\\r]+/", " ", $sql);
    $callsign=$_SESSION['callsign'];
    
    $username = $_SESSION['admin_username'];
    $email = $_SESSION['admin_email'];
    $password = $_SESSION['admin_password'];
    $SALT = $_SESSION['SALT'];
    //$sql = preg_replace("/[?]+/", $callsign, $sql);
    
    $name = $_SESSION['brand'];
    $designation = "";
    $frequency = $_SESSION['frequency'];
    $website = $_SESSION['website'];
    $address = "";
    $mainPhone = $_SESSION['req_phone'];
    $mgrPhone = $_SESSION['mgrPhone'];
    $con = $mysqli->prepare(
            "insert into `station` (callsign,stationname,Designation,"
            . "frequency,website,address,boothphone,directorphone) "
            . "values ( ?, ?, ?, ?, ?, ?, ?, ?)"
            );
    if($con===false){
        trigger_error($tps->mysqli->error,E_USER_ERROR);
    }
    $con->bind_param("ssssssss", $callsign, $name, $designation,
            $frequency, $website, $address, $mainPhone, $mgrPhone);
    $con->execute();
    if($con === false){
        trigger_error($tps->mysqli->error,E_USER_ERROR);
    }
    $station = $con->insert_id;
    $con->close();
    
    
    $SQL_Statements=explode(";",$sql);
    $PER_Statements=explode(";",$permissions);
    /*
    if (!($mysqli->query($sql))) {
        $return=["status"=>"Error","Result"=>"Query failed: (" . $mysqli->errno . ") " . $mysqli->error];
        $error_check=TRUE;
        echo "ERROR:" . $sql ."<br><br>".$mysqli->error."<br><br>";
    }
    */
    //
    //var_dump($sql);
    /* Prepared statement, stage 1: prepare */
    error_reporting(0);
    $error_check=false;
    $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    try{
        $mysqli->autocommit(FALSE);
        foreach($SQL_Statements as $EXEC){
            //if(strlen($EXEC)<6){
                $EXEC = str_replace(array("\r", "\n"), '', $EXEC);
            //}
            /*
            else{
                $EXEC = str_replace(array("\r", "\n"), ' ', $EXEC);
            }*/
            if(!empty($EXEC)){
                $EXEC.=";";
                $access = 2;
                //$mysqli->query($EXEC);
                if ((!$stmt = $mysqli->prepare($EXEC))) {
                    $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error,"Query"=>$EXEC];
                    $error_check=TRUE;
                    if($mysqli->errno==1142){
                        http_response_code(403);
                    }
                    else{
                        $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error,"Query"=>$EXEC];
                        http_response_code(500);
                    }
                    echo "ERROR:" . $EXEC ."<br><br>".$mysqli->error."<br><br>";
                    throw new Exception($mysqli->error,$mysqli->errno);
                }
                else{
                    
                    $stmt->bind_param("is", $access ,$callsign);
                    if(!$stmt->execute()){
                        http_response_code(409);
                        $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error,"Query"=>$EXEC];
                    }
                    $stmt->close();
                    /*if(strpos($EXEC, "CREATE SCHEMA IF NOT EXISTS")){
                        $mysqli->commit();
                    }*/
                    //echo $EXEC . "<br><br>";
                }
            }
        }
        foreach($PER_Statements as $EXEC){
            if(strlen($EXEC)<6){
                $EXEC = str_replace(array("\r", "\n"), '', $EXEC);
            }/*
            else{
                $EXEC = str_replace(array("\r", "\n"), ' ', $EXEC);
            }*/
            if(!empty($EXEC)){
                $EXEC.=";";
                $access=2;
                //$mysqli->query($EXEC);
                if ((!$stmt = $mysqli->prepare($EXEC))) {
                    $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error,"Query"=>$EXEC];
                    $error_check=TRUE;
                    if($mysqli->errno==1142){
                        http_response_code(403);
                    }
                    else{
                        $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error,"Query"=>$EXEC];
                        http_response_code(500);
                    }
                    echo "ERROR:" . $EXEC ."<br><br>".$mysqli->error."<br><br>";
                    throw new Exception($mysqli->error,$mysqli->errno);
                }
                else{
                    
                    $stmt->bind_param("issss", $access ,$username, $email, $password, $SALT);
                    //$insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
                    if(!$stmt->execute()){
                        http_response_code(409);
                        $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error,"Query"=>$EXEC];
                    }
                    $stmt->close();
                    /*if(strpos($EXEC, "CREATE SCHEMA IF NOT EXISTS")){
                        $mysqli->commit();
                    }*/
                    //echo $EXEC . "<br><br>";
                }
            }
        }
        if($error_check===FALSE){
        $mysqli->commit();
        }
        else{
            throw new Exception($mysqli->error,$mysqli->errno);
        }
    } catch (Exception $e){
        $return=["status"=>"Error","Result"=>array("Query"=>$e->getMessage(),"Code"=>$e->getCode())];
        $error_check=TRUE;
        $mysqli->rollback();
    }
    //$mysqli->commit();
    $mysqli->autocommit(TRUE);
    $functions = \file_get_contents("setup.functions.sql");
    //$functions = preg_replace("/[\\n\\r]+/", ' ' , $functions);
    $functions = preg_replace("/[?]+/", $callsign, $functions);
    
    //temporarily removed
    /*if($mysqli->query($functions)!=TRUE){
        $return=["status"=>"Error","Result"=>"Query failed: (" . $mysqli->errno . ") " . $mysqli->error];
        $error_check=TRUE;
        //echo "<br>".$functions."<br>";
    }*/
    
    if($error_check===false && empty($return)){
        $return=["status"=>"Complete","Result"=>"Complete"];
    }
    //$mysqli->commit();
    /*
    if (!($stmt = $mysqli->prepare($sql))) {
        $return=["status"=>"Error","Result"=>"Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error];
    }
    else{
        //prepared statement, stage 2: bind and execute
        $db=$_SESSION['database'];
        var_dump($db);
        if (!$stmt->bind_param("s", $db)) {
            $return=["status"=>"Error","Result"=>"binding parameters failed: (" . $stmt->errno . ") " . $stmt->error];
        }
        else{
            if(!($stmt->execute())){
                $return=["status"=>"Error","Result"=>"Execution Failed: (" . $stmt->errno . ") " . $stmt->error];
            }
            else{
                $return=["status"=>"Complete","Result"=>"Complete"];
            }
            $stmt->close();
        }
    }*/
    $mysqli->close();
}
header('Content-type: application/json');
print json_encode($return);
