<?php

/**
 * Class flyRegistry
 * Store for global variables.
 *
 */
class flyRegistry
{
    static $registry =array();

    /**
     * Get variable from registry
     * @param $key
     * @param bool|false $default
     * @return bool
     */
    public static function get($key, $default =false){
        return (isset(self::$registry[$key])) ? self::$registry[$key] : $default ;
    }


    /**
     * Put variable to registry
     * @param $key
     * @param $value
     */
    public static function set($key, $value){
        self::$registry[$key] = $value;
    }

}
