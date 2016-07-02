<?php

namespace Empathy\MVC\Util\Testing;

use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;




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


    protected function makeFakeBootstrap() {
        // use eaa archive as root
        $doc_root = realpath(
            dirname(realpath(__FILE__)).'/../../../../eaa/'
        );
        
        $this->setConfig('NAME', 'empathytest');
        $this->setConfig('TITLE', 'empathy testing');
        $this->setConfig('DOC_ROOT', $doc_root);
        $this->setConfig('WEB_ROOT' , 'localhost/empathytest');
        $this->setConfig('PUBLIC_DIR', '/public_html');

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
                'config' => '{ "testing": true }'
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
        $empathy->init();

        $bootstrap = $container->get('Bootstrap');
        return $bootstrap;
    }

}
