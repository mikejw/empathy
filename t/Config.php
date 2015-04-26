<?php

namespace ESuite;

class Config
{
    private static $items;

    public static function init()
    {
        self::$items = array();

        $base = realpath(dirname(realpath(__FILE__)));
        $config = $base.'/config.yml';

        $config_arr = Util\YAML::load($config);
        foreach ($config_arr as $index => $value) {
            self::$items[$index] = $value;
        }

        self::set('base', dirname(realpath(__FILE__)));
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