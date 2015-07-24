<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Used to silence annoying warnings so we can load the proper timezone...
date_default_timezone_set('UTC');
session_start();
require_once (__DIR__.DIRECTORY_SEPARATOR."CONFIG.php");
date_default_timezone_set($timezone);

if(is_null(filter_input(INPUT_GET,'legacy'))){
    require_once('public/index.php');
}
else{
    require_once('legacy_controller.php');
    
}
