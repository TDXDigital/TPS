<?php
/*
 * provides "routing" for Setup.    
*/
if(!isset($PAGE)||$PAGE=='undefined'){
    echo "<span class='ui-state ui-state-error>Welcome to the TPS Broadcast Setup, please start at welcome.</span>";
    //header('location:?q=wel');
}
else{
    switch ($PAGE){
        case "wel":
            include_once("setup.start.php");
        break;
        case "lic":
            include_once("setup.licence.php");
        break;
        case "db":
            include_once("setup.database.php");
        break;
        case "settings":
            include_once("setup.settings.php");
        break;
        case "auth":
            include_once("setup.auth.php");
        break;
        case "rev":
            include_once("setup.review.php");
        break;
        case "install":
            include_once("setup.run.php");
        break;
        case "done":
            include_once("setup.done.php");
        break;
        case "fail":
            include_once("setup.fail.php");
        break;
        default:
            echo "undefined value: ".$PAGE;
            //header('location:./');
    }
}

?>