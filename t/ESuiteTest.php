<?php

namespace ESuite;

use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;
use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\DI;


abstract class ESuiteTest extends \PHPUnit\Framework\TestCase
{
    protected function makeFakeBootstrap($persistentMode=true)
    {
        // use eaa archive as root
        $doc_root = realpath(dirname(realpath(__FILE__)).'/../eaa/');
        $container = DI::init($doc_root, $persistentMode);
        $empathy = $container->get('Empathy');
        $empathy->init();
        $bootstrap = $container->get('Bootstrap');
        return $bootstrap;
    }
}
