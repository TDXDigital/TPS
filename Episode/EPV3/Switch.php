<?php
	session_start();

    require_once "../../TPSBIN/cron.php";
    include_once '../../CONFIG.php';

    // generate cron
    $switch_query = new TPS_Cron();

    // execute query with mute off
    if($switch_enabled){
    	/*
    	 * system should detect if switch is enabled from a DB ssettings
    	 */
    	$switch_query->update_switch(FALSE,$switch);
    	
    }
    // Replaced code with function of cron script

?>
