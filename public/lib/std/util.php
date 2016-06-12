<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2016-06-11
 * Time: 20:06
 */
namespace TPS;

class util{
    public static function get($array, $key, $default = null) {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}