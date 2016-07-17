<?php
	session_start();
date_default_timezone_set($_SESSION['TimeZone']);

    require_once "../../../TPSBIN/cron.php";
    include_once '../../../CONFIG.php';

    // generate cron
    $switch_query = new TPS_Cron();

    // execute query with mute off
    if($switch_enabled){
    	/*
    	 * system should detect if switch is enabled from a DB ssettings
    	 */
        if(!$switch_query->update_switch_ACS8p2(FALSE,$switch)){
            http_response_code(403);
        }

    }
    // Replaced code with function of cron script

