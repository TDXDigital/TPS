<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$CHECK = filter_input(INPUT_POST, "id" , FILTER_SANITIZE_STRING);


$result = array();
$dbxml = simplexml_load_file("../TPSBIN/XML/DBSETTINGS.xml");
if(!empty($dbxml))
{
    foreach( $dbxml->SERVER as $convar):
        if($convar->ID===$CHECK){
            $result[]=["AUTH"=>$convar->AUTH,"LOGO"=>$convar->LOGOPATH];
            //$result[]=["server"=>$convar->ID,"NAME"=>$convar->NAME,"LOGO"=>$convar->LOGOPATH];
        }
    endforeach;
    header('Content-Type: application/json');
    echo json_encode($result);
}
else{
    //error (should send to setup before this is needed)
}