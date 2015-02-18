<?php
	session_start();

    require_once "../../TPSBIN/cron.php";
    error_reporting(0);
    if(strtolower($_GET['q'])=='s'){
        // generate cron
        $switch_query = new TPS_Cron();

        // execute query with mute off
        $switch_query->update_switch(FALSE,"ckxu3400lg.local.ckxu.com");

        // Replaced code with function of cron script
    }
    elseif(strtolower($_GET['q'])=='np'){
        // disabled due to performance lag
        
        // generate cron
        $foo_query = new TPS_Cron();

        // execute query with mute off
        $foo_query->get_now_playing_foobar();

        // Replaced code with function of cron script
    }
?>
