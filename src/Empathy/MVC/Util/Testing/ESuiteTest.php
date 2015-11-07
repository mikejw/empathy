<?php

namespace Empathy\MVC\Util\Testing;

use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;
use Mockery as m;



/**
 * Empathy test suite base class
 * @file            Empathy/MVC/Util/Testing/ESuite.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
abstract class ESuiteTest extends \PHPUnit_Framework_TestCase
{
    private $boot;
    
    protected function makeBootstrap()
    {
        global $base_dir;
        $this->boot = new \Empathy\MVC\Empathy(realpath($base_dir), true);
    }


    protected function appRequest($uri, $mode=CLIMode::CAPTURED)
    {
        if (!isset($this->boot)) {
            throw new \Exception('app not inited.');
        } else {
            CLI::setReqMode($mode);
            return CLI::request($this->boot, $uri);            
        }
    }
}
