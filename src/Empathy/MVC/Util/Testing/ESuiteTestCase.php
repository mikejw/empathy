<?php

namespace Empathy\MVC\Util\Testing;

use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;
use Empathy\MVC\Config as EmpConfig;

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
abstract class ESuiteTestCase extends \PHPUnit_Framework_TestCase
{
    private $boot;
    
    protected function makeBootstrap()
    {
        global $base_dir;

        $container = \Empathy\MVC\DI::init($base_dir, true);
        $empathy = $container->get('Empathy');
        $empathy->init();
        $this->boot = $empathy;
    }


    protected function appRequest($uri, $mode = CLIMode::CAPTURED)
    {
        if (!isset($this->boot)) {
            throw new \Exception('app not inited.');
        } else {
            CLI::setReqMode($mode);
            return CLI::request($this->boot, $uri);
        }
    }


    protected function makeFakeBootstrap($testingMode = \Empathy\MVC\Plugin\ELibs::TESTING_EMPATHY)
    {

        // use eaa archive as root
        $doc_root = realpath(
            dirname(realpath(__FILE__)).'/../../../../../eaa/'
        );

        $dummyBootOptions = array(
            'default_module' => 'foo',
            'dynamic_module' => null,
            'debug_mode' => false,
            'environment' => 'dev',
            'handle_errors' => false
        );
        $plugins = array(
            array(
                'name' => 'ELibs',
                'version' => '1.0',
                'config' => '{ "testing": '.$testingMode.' }'
            ),
            array(
                'name' => 'Smarty',
                'version' => '1.0',
                'class_path' => 'Smarty/Smarty.class.php',
                'class_name' => '\Smarty',
                'loader' => ''
            )
        );

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

        $bootstrap = $container->get('Bootstrap');
        return $bootstrap;
    }


    protected function setConfig($key, $value)
    {
        EmpConfig::store($key, $value);
    }
}
