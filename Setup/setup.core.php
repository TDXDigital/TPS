<?php
/*
 * provides "routing" for Setup.    
*/
if(count(get_included_files()) ==1){
    http_response_code(403);
    $refusal = "<h1>403 Forbidden</h1><p>The requested resource cannot"
            . " be accessed directly</p>";
    die($refusal);
}
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
        case "review":
            include_once("setup.review.php");
        break;
        case "install":
            include_once("setup.run.php");
        break;
        case "complete":
            include_once("setup.complete.php");
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