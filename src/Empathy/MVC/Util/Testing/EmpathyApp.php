<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing;

use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\DI;
use Empathy\MVC\Util\CLI;
use Empathy\MVC\Util\CLIMode;
use Empathy\MVC\Util\Testing\Util\DB;

/**
 * Shared helpers for bootstrapping Empathy in tests (PHPUnit or Pest).
 */
final class EmpathyApp
{
    private ?\Empathy\MVC\Empathy $empathy = null;

    public function makeBootstrap(): void
    {
        global $base_dir;

        $container = DI::init($base_dir, true);
        $instance = $container->get('Empathy');
        $instance->init($container->get('Bootstrap'));
        $this->empathy = $instance;
    }

    public function appRequest(string $uri, int $mode = CLIMode::CAPTURED): mixed
    {
        if (!$this->empathy instanceof \Empathy\MVC\Empathy) {
            throw new \Exception('app not inited.');
        }

        CLI::setReqMode($mode);

        return CLI::request($this->empathy, $uri);
    }

    public function makeFakeBootstrap(int $testingMode = \Empathy\MVC\Plugin\ELibs::TESTING_EMPATHY): \Empathy\MVC\Bootstrap
    {
        $dummyBootOptions = [
            'default_module' => 'front',
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

        $doc_root = $this->getDocRoot();
        $container = DI::init($doc_root, true);
        $empathy = $container->get('Empathy');
        $empathy->setBootOptions($dummyBootOptions);
        $empathy->setPlugins($plugins);

        $this->setConfig('NAME', 'empathytest');
        $this->setConfig('TITLE', 'empathy testing');
        $this->setConfig('DOC_ROOT', $doc_root);
        $this->setConfig('WEB_ROOT', 'www.dev.org');
        $this->setConfig('PUBLIC_DIR', '');

        DB::loadDefDBCreds();

        $empathy->init($container->get('Bootstrap'));

        if (
            class_exists(\Empathy\ELib\User\CurrentUser::class) &&
            class_exists(\Empathy\ELib\Storage\UserItem::class)
        ) {
            DI::getContainer()->set('UserModel', \Empathy\ELib\Storage\UserItem::class);
            DI::getContainer()->set('CurrentUser', new \Empathy\ELib\User\CurrentUser());
        }

        return $container->get('Bootstrap');
    }

    public function setConfig(string $key, mixed $value): void
    {
        EmpConfig::store($key, $value);
    }

    private function getDocRoot(): string
    {
        $selfPath = realpath(__FILE__);
        if ($selfPath === false) {
            throw new \RuntimeException('Could not resolve EmpathyApp path');
        }
        $doc_root = realpath(dirname($selfPath).'/../../../../../eaa/');
        if ($doc_root === false) {
            throw new \RuntimeException('eaa fixture root not found');
        }

        return $doc_root;
    }
}
