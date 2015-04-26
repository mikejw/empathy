<?php

/**
 * Empathy/MVC/Util/Lib.php
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 *
 * @category  Framework
 * @package   Empathy
 * @author    Mike Whiting <mail@mikejw.co.uk>
 * @copyright 2008-2013 Mike Whiting
 * @license   http://www.gnu.org/licenses/gpl-3.0-standalone.html GPL v3.0
 * @link      http://empathyphp.co.uk
 *
 */
namespace Empathy\MVC\Util;

/**
 * Empathy Lib Utility class.
 * Manage libs.
 *
 * @category Framework
 * @package  Empathy
 * @author   Mike Whiting <mail@mikejw.co.uk>
 * @license  http://www.gnu.org/licenses/gpl-3.0-standalone.html GPL v3.0
 * @link     http://empathyphp.co.uk
 */
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
