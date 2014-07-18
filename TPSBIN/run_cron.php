<?php
    
if(isset($_SESSION)){
   session_start();
}
require_once "cron.php";

if(isset($_GET["episode"]))
{
    echo "performing grading on episode(s)";
    $episode = new TPS_Cron();
    $episode->grade_episode($_GET['episode'],FALSE);
}

?>