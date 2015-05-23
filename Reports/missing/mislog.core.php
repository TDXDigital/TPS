<?php
    
if(!isset($PAGE)){
    echo "<span class='ui-state ui-state-error>Please Select a View</span>";
}
else{
    switch ($PAGE){
        case "rep":
            include_once("mislog.rep.php");
        break;
        case "active":
            echo "Active Commercials to be listed for edit";
        break;
        case "ver":
            include_once("traffic.verify_ad.php");
        break;
        default:
            echo "undefined value: ".$PAGE;
    }
}

?>