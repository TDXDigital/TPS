<?php
	session_start();

    require_once "../../TPSBIN/cron.php";

    // generate cron
    $switch_query = new TPS_Cron();

    // execute query with mute off
    if(FALSE){
    	/*
    	 * system should detect if switch is enabled from a DB ssettings
    	 */
    	$switch_query->update_switch(FALSE,"ckxu3400lg.local.ckxu.com");
    	
    }
    // Replaced code with function of cron script

?>
