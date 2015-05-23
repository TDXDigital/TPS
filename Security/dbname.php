<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$CHECK = filter_input(INPUT_GET, "id" , FILTER_SANITIZE_STRING);

$result = array();
$dbxml = simplexml_load_file("../TPSBIN/XML/DBSETTINGS.xml");
if(!empty($dbxml))
{
    foreach( $dbxml->SERVER as $convar):
        if($CHECK === (string)$convar->ID){
            $result[]=['server'=>(string)$convar->ID,'NAME'=>(string)$convar->NAME,'LOGO'=>(string)$convar->LOGOPATH];
        }
    endforeach;
    header('Content-Type: application/json');
    echo json_encode($result);
}
else{
    //error (should send to setup before this is needed)
}