<?php

/**
 * Registry - object-wrapper for storing
 * all instances of global objects
 **/
class Registry
{
    private static $objects = array();

    // Returns object from registry
    public static function get($object_name)
    {
        if (isset(self::$objects[$object_name])) {
            return self::$objects[$object_name];
        }
    }

    // Saves object in registry
    public static function set($object_name, $object)
    {
        self::$objects[$object_name] = $object;
    }
}

?>