<?php

/**
 * Empathy/MVC/Util/Lib.php
 *
 * @category  Framework
 * @package   Empathy
 * @author    Mike Whiting <mikejw3@gmail.com>
 * @copyright 2008-2015 Mike Whiting
 * @license   See LICENSE
 * @link      http://empathyphp.co.uk
 *
 */
namespace Empathy\MVC\Util;

class Lib
{
    /**
     * Add custom to path to include path.
     *
     * @param string $path   The custom path
     *
     */
    public static function addToIncludePath($path)
    {
        $existing_include_path = get_include_path();
        ini_set(
            'include_path',
            $existing_include_path
            .PATH_SEPARATOR
            .$path
        );
    }
}
