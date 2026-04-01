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

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin;

/**
 * Cassandra integration.
 *
 * @author Mike Whiting mike@ai-em.net
 */
class Cassandra extends Plugin
{
    /**
     * Imports cassandra code.
     */
    public function __construct()
    {
        if (file_exists('Cassandra/gen-php/cassandra/Cassandra.php')) {
            include __DIR__ . '/Cassandra/gen-php/cassandra/Cassandra.php';
        }

        if (file_exists('Cassandra/gen-php/cassandra/Types.php')) {
            include __DIR__ . '/Cassandra/gen-php/cassandra/Types.php';
        }
    }
}
