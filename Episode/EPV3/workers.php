<?php
    session_start();
    
    // Get values passed to workers
    $action = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
    $server = filter_input(INPUT_GET, 's' , FILTER_SANITIZE_STRING)? :"ckxu3400lg.local.ckxu.com";
        
    //load cron items
    require_once "../../TPSBIN/cron.php";
    
    //run request with no errors (handle errors on other end)
    error_reporting(0);
    if(strtolower($action)==='s'){
        // generate cron
        $switch_query = new TPS_Cron();

        // execute query with mute off
        $switch_query->update_switch(FALSE,$server);

        // Replaced code with function of cron script
    }
    elseif(strtolower($action)==='np'){
        // disabled due to performance lag
        
        // generate cron
        $foo_query = new TPS_Cron();

        // execute query with mute off
        $foo_query->get_now_playing_foobar();

        // Replaced code with function of cron script
    }
