<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(is_null(filter_input(INPUT_GET,'twig'))){
    require_once('legacy_controller.php');
}
else{
    require_once('public/index.php');
}
