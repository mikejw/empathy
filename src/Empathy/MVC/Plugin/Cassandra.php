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

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Cassandra integration.
 *
 * @author Mike Whiting mike@ai-em.net
 */
class Cassandra extends Plugin
{

    /**
     * Imports cassandra code.
     *
     * @return null
     */
    public function __construct()
    {
        require 'Cassandra/gen-php/cassandra/Cassandra.php';
        require 'Cassandra/gen-php/cassandra/Types.php';
    }
}
