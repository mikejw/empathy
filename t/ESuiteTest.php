<?php

declare(strict_types=1);

namespace ESuite;

use Empathy\MVC\DI;

abstract class ESuiteTest extends \PHPUnit\Framework\TestCase
{
    protected function makeFakeBootstrap($persistentMode = true)
    {
        // use eaa archive as root
        $doc_root = realpath(dirname(realpath(__FILE__)).'/../eaa/');
        $container = DI::init($doc_root, $persistentMode);
        $empathy = $container->get('Empathy');
        $empathy->init();
        return $container->get('Bootstrap');
    }
}
