<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$CHECKDB=false;

$return=[];
include_once "../TPSBIN/functions.php";
    if(!isset($_SESSION)){
        sec_session_start();
    }

    define(HOST,$_SESSION['host']);
    define(PASSWORD,$_SESSION['password']);
    define(USER,$_SESSION['user']);
    
    /*
     * Connect to DB, Do not define DATABASE
     * Could not use db_connect as DATABASE is needed.
     */
if(!$mysqli = new mysqli(HOST, USER, PASSWORD)){
    /*
     * return 403
     * cancel run (exit)
     */
    \header('HTTP/1.1 403 Access Denied', true, 403);
    exit;
    
    //$return=["status"=>"403","Result"=>$mysqli->connect_error,"e-code"=>$mysqli->connect_errno];
}
elseif(!isset($_SESSION['database'])){
    /*
     * cannot proceede return 400
     * exit run
     */
    \header('HTTP/1.1 400 Bad Request', true, 400);
    exit;
}
/*
 * Connection Established.
 */
else{
    if($mysqli->select_db($_SESSION['database'])){
        if($CHECKDB){
            $return=["status"=>"Error","Result"=>"Database Exists, cannot continue"];
        }
    }
    $sql = \file_get_contents("setup.createdb.sql");
    $sql = preg_replace("/[\\n\\r]+/", " ", $sql);
    $DB_NAME=$_SESSION['database'];
    $sql = preg_replace("/[?]+/", $DB_NAME, $sql);
    
    
    $SQL_Statements=explode(";",$sql);
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
    
    $error_check=false;
    foreach($SQL_Statements as $EXEC){
        if(!empty($EXEC)){
            if (!($mysqli->query($EXEC))) {
                $return=["status"=>"Error","Result"=>"Query failed: (" . $mysqli->errno . ") " . $mysqli->error];
                $error_check=TRUE;
                echo "ERROR:" . $EXEC ."<br><br>".$mysqli->error."<br><br>";
            }
            else{
                //echo $EXEC . "<br><br>";
            }
            
        }
    }
    if($error_check===false){
        $return=["status"=>"Complete","Result"=>"Complete"];
    }
    $mysqli->commit();
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