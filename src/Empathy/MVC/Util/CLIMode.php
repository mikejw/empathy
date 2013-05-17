<?php

/**
 * Empathy/MVC/Util/CLIMode.php 
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
 * Empathy Utility class for specifying CLI request modes.
 *
 * @category Framework
 * @package  Empathy
 * @author   Mike Whiting <mail@mikejw.co.uk>
 * @license  http://www.gnu.org/licenses/gpl-3.0-standalone.html GPL v3.0
 * @link     http://empathyphp.co.uk
 */
class CLIMode
{
    const TIMED = 0;
    const CAPTURED = 1;
    const FAKED = 2;
    const STREAMED = 3;

}
