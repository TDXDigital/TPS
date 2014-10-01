<?php
	session_start();

    require_once "../../TPSBIN/cron.php";
    if(strtolower($_GET['q'])=='s'){
        // generate cron
        $switch_query = new TPS_Cron();

        // execute query with mute off
        $switch_query->update_switch(FALSE,"ckxu3400lg.local.ckxu.com");

        // Replaced code with function of cron script
    }
    elseif(strtolower($_GET['q'])=='np'){
        // generate cron
        $switch_query = new TPS_Cron();

        // execute query with mute off
        $switch_query->get_now_playing_foobar();

        // Replaced code with function of cron script
    }
?>