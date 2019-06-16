<?php

/**
 * Empathy/MVC/Util/CLIMode.php
 *
 *
 * @category  Framework
 * @package   Empathy
 * @author    Mike Whiting <mikejw3@gmail.com>
 * @copyright 2008-2013 Mike Whiting
 * @license   See LICENSE
 * @link      http://empathyphp.co.uk
 *
 */

namespace Empathy\MVC\Util;

class CLIMode
{
    const TIMED = 0;
    const CAPTURED = 1;
    const FAKED = 2;
    const STREAMED = 3;
}
