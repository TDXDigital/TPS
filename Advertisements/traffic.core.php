<?php
    
if(!isset($PAGE)){
    echo "<span class='ui-state ui-state-error>Please Select a View</span>";
}
else{
    switch ($PAGE){
        case "new":
            include_once("traffic.new.php");
        break;
        case "active":
            echo "Active Commercials to be listed for edit";
        break;
        default:
            echo "undefined value: ".$PAGE;
    }
}

?>