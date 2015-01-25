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
        if($convar->ID==$CHECK){
            $result[]=["AUTH"=>$convar->AUTH,"LOGO"=>$convar->LOGOPATH];
            //$result[]=["server"=>$convar->ID,"NAME"=>$convar->NAME,"LOGO"=>$convar->LOGOPATH];
        }
    endforeach;
    if(sizeof($result,COUNT_RECURSIVE)<1){
        $result[]=["AUTH"=>"NOT_FOUND","LOGO"=>"NOT_FOUND"];
    }
    header('Content-Type: application/json');
    //console.log($result);
    echo json_encode($result);
}
else{
    //error (should send to setup before this is needed)
    $result[]=["AUTH"=>"ERROR","LOGO"=>"ERROR"];
    echo json_encode($result);
    echo "ERROR: Empty request";
}