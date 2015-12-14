<?php
    if(file_exists("../TPSBIN/XML/DBSETTINGS.xml")){
        http_response_code(403);
        $refusal = "<h1>403 Forbidden</h1><p>Your request cannot proceed as the"
                . " this server has already been configured.</p>";
        die($refusal);
    }

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