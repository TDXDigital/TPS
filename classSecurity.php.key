<?php

/* 
 * Due to the sensitive nature of this file 
 * This file MUST be set to either permission 400 (recommended), 500, or 600
 */

Class Security {
    private $p_key = '';
    
    public function _construct(){
        if($p_key === ''){
            $p_key = '!@#$%^&*';
        }
    }
    // MUST BE DEPRECATED ASAP
    public function get_key() {
        return $p_key;
    }
}
