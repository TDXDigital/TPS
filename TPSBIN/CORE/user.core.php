<?php
    
if(!isset($PAGE)||$PAGE=='undefined'){
    echo "<span class='ui-state ui-state-error>Please Select a View</span>";
}
else{
    switch ($PAGE){
        case "new":
            include_once("new_episode.php");
        break;
        case "load":
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
            //echo "undefined value: ".$PAGE;
            header('location:./');
    }
}

?>