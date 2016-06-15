<?php
    
if(!isset($PAGE)){
    echo "<span class='ui-state ui-state-error>Please Select a View</span>";
}
else{
    switch ($PAGE){
        case "history":
            include_once("live_stat.php");
        break;
        case "t_songs":
            include_once("top_songs.php");
        break;
        case "t_albums":
            include_once("top_albums.php");
        break;
        case "requests":
            include_once("request.php");
        break;
        case "tcpc":
            include_once("commands.php");
        break;
        default:
            echo "undefined value: ".$PAGE;
    }
}

?>