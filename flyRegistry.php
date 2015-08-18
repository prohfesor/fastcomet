<?php

class flyRegistry
{
    static $registry =array();

    public static function get($key, $default =false){
        return (isset(self::$registry[$key])) ? self::$registry[$key] : $default ;
    }

    public static function set($key, $value){
        self::$registry[$key] = $value;
    }

}
