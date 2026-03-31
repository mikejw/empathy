<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

class Config
{
    /** @var array<string, mixed> */
    private static array $items = [];

    public static function init(string $base): void
    {
        $utilDir = realpath(dirname(__FILE__));
        self::$items = [];
        $config = $base.'/config.yml';
        $config_arr = YAML::load($config);
        foreach ($config_arr as $index => $value) {
            self::$items[$index] = $value;
        }
        self::set('base', $base);
        self::set('util_dir', $utilDir);
    }

    public static function get(string $key): mixed
    {
        $val = false;
        if (isset(self::$items[$key])) {
            $val = self::$items[$key];
        }
        return $val;
    }

    public static function set(string $key, mixed $value): void
    {
        self::$items[$key] = $value;
    }
}
