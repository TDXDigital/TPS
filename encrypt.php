<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
date_default_timezone_set('UTM');
include "TPSBIN".DIRECTORY_SEPARATOR."functions.php";
$value = filter_input(INPUT_GET,'q');
print easy_crypt($ENCR_KEY, $value);
