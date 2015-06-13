<?php
    
if(!isset($PAGE)){
    echo "<span class='ui-state ui-state-error>Please Select a View</span>";
}
else{
    switch ($PAGE){
        case "new":
            include_once("playlist.receiving.php");
        break;
        case "report":
            include_once 'playlist.report.php';
        case "active":
            echo "Not Implemented";
        break;
        case "ver":
            echo "Not Implemented";//include_once("traffic.verify_ad.php");
        break;
        default:
            echo "undefined value: ".$PAGE;
    }
}

?>