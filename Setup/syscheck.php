<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function Extensions( $extensions ){
    $minimum_requirements = [
        "mysqli",
        "mysql",
        "openssl"
    ];
    $optional_requirements = [
        "mysqlnd"
    ];
    
    //$checkreqs = $minimum_requirements + $optional_requirements;
    $checkreqs = array_merge($minimum_requirements,$optional_requirements);
    $php_extensions = get_loaded_extensions();
    $installed_extensions = array_intersect($php_extensions,$checkreqs);
    $missing_extensions = array_diff($checkreqs, $installed_extensions);
    if(sizeof($missing_extensions)!=0){
        $status = FALSE;
    }
    else{
        $status = TRUE;
    }
    $result = [
        "Status" => $status,
        "Passed" => $installed_extensions,
        "Failed" => $missing_extensions,
        "Checked" => $checkreqs
    ];
    return $result;
}

function Versions( $extensions ){
    
}

/* AJAX check  */
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $JSON = Extensions("");
    print json_encode($JSON);
}