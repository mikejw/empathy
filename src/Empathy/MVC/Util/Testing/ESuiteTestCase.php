<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing;

use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;

/**
 * Empathy test suite base class
 * @file            Empathy/MVC/Util/Testing/ESuite.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
abstract class ESuiteTestCase extends \PHPUnit\Framework\TestCase
{
    private ?\Empathy\MVC\Empathy $boot = null;

    protected function makeBootstrap(): void
    {
        global $base_dir;

        $container = \Empathy\MVC\DI::init($base_dir, true);
        $empathy = $container->get('Empathy');
        $empathy->init();
        $this->boot = $empathy;
    }


    protected function appRequest(string $uri, int $mode = CLIMode::CAPTURED): mixed
    {
        if (!$this->boot instanceof \Empathy\MVC\Empathy) {
            throw new \Exception('app not inited.');
        } else {
            CLI::setReqMode($mode);
            return CLI::request($this->boot, $uri);
        }
    }


    protected function makeFakeBootstrap(int $testingMode = \Empathy\MVC\Plugin\ELibs::TESTING_EMPATHY): \Empathy\MVC\Bootstrap
    {
        // use eaa archive as root
        $selfPath = realpath(__FILE__);
        if ($selfPath === false) {
            throw new \RuntimeException('Could not resolve ESuiteTestCase path');
        }
        $doc_root = realpath(dirname($selfPath).'/../../../../../eaa/');
        if ($doc_root === false) {
            throw new \RuntimeException('eaa fixture root not found');
        }

        $dummyBootOptions = [
            'default_module' => 'foo',
            'dynamic_module' => null,
            'debug_mode' => false,
            'environment' => 'dev',
            'handle_errors' => false,
        ];
        $plugins = [
            [
                'name' => 'ELibs',
                'version' => '1.0',
                'config' => '{ "testing": '.$testingMode.' }',
            ],
            [
                'name' => 'Smarty',
                'version' => '1.0',
                'class_path' => 'Smarty/libs/Smarty.class.php',
                'class_name' => '\Smarty',
                'loader' => '',
            ],
        ];

        $container = \Empathy\MVC\DI::init($doc_root, true);
        $empathy = $container->get('Empathy');
        $empathy->setBootOptions($dummyBootOptions);
        $empathy->setPlugins($plugins);

        // override config
        $this->setConfig('NAME', 'empathytest');
        $this->setConfig('TITLE', 'empathy testing');
        $this->setConfig('DOC_ROOT', $doc_root);
        $this->setConfig('WEB_ROOT', 'localhost/empathytest');
        $this->setConfig('PUBLIC_DIR', '/public_html');


        $empathy->init();
        return $container->get('Bootstrap');
    }


    protected function setConfig(string $key, mixed $value): void
    {
        EmpConfig::store($key, $value);
    }
}

