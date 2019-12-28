<?php
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
    private static $items = array();

    /**
     * Return a piece of config.
     *
     * @param string $key The config key.
     * @return string Config.
     */
    public static function get($key)
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
     * @param mixed Data to store against key.
     * @return null
     */
    public static function store($key, $data)
    {
        self::$items[$key] = $data;
    }

    /**
     * Simple dump of config.
     * @return null
     */
    public static function dump()
    {
        print_r(self::$items);
    }
}
