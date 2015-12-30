<?php

namespace ESuite\Util;

class Config
{
    private static $items;

    public static function init()
    {
        self::$items = array();
        $base = realpath(dirname(__FILE__).'/../');
        $config = $base.'/config.yml';
        $config_arr = YAML::load($config);
        foreach ($config_arr as $index => $value) {
            self::$items[$index] = $value;
        }
        self::set('base', $base);
    }

    public static function get($key)
    {
        $val = false;
        if (isset(self::$items[$key])) {
            $val = self::$items[$key];
        }
        return $val;
    }

    public static function set($key, $value)
    {
        self::$items[$key] = $value;
    }
}