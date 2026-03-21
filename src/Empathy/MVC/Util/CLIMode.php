<?php

declare(strict_types=1);

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
    public const TIMED = 0;
    public const CAPTURED = 1;
    public const FAKED = 2;
    public const STREAMED = 3;
}
