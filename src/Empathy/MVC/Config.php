<?php

declare(strict_types=1);
/**
 * This file is part of the Empathy package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @copyright 2008-2016 Mike Whiting
 * @license  See LICENSE
 * @link      http://www.empathyphp.co.uk
 */

namespace Empathy\MVC;

/**
 * Based on Stash class but static.
 * All config keys should be uppercase.
 *
 * @author Mike Whiting mike@ai-em.net
 */
class Config
{
    /**
     * Initialise empty config;
     */
    private static $items = [];

    /**
     * Return a piece of config.
     *
     * @param string $key The config key.
     * @return mixed Config.
     */
    public static function get(string $key): mixed
    {
        if (!isset(self::$items[$key])) {
            return false;
        } else {
            return self::$items[$key];
        }
    }

    /**
     * Store some config.
     *
     * @param string $key The config key.
     * @param mixed $data The data to store against key.
     * @return void
     */
    public static function store(string $key, mixed $data): void
    {
        self::$items[$key] = $data;
    }

    /**
     * Simple dump of config.
     * @return void
     */
    public static function dump(): void
    {
        print_r(self::$items);
    }
}
