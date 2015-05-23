<?php

// based on Stash class but static
// all config keys should be uppercase

namespace Empathy\MVC;

class Config
{
    private static $items = array();


    public static function get($key)
    {
        if (!isset(self::$items[$key])) {
            return false;
        } else {
            return self::$items[$key];
        }
    }

    public static function store($key, $data)
    {
        self::$items[$key] = $data;
    }

    public static function dump()
    {
        print_r(self::$items);
    }
}
