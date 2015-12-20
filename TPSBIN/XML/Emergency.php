<?php
    
set_include_path("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR);
try{
    include 'public/lib/emergencyAlert.php';

    $station = filter_input(INPUT_GET, "station", FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_GET, "location", FILTER_SANITIZE_STRING);
    $format = filter_input(INPUT_GET, "format", FILTER_SANITIZE_STRING)?:"json";

    $alerts = new \TPS\emergencyAlert($station,$location,$format);
    print $alerts->run();
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
restore_include_path();
