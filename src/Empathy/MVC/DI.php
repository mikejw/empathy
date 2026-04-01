<?php

declare(strict_types=1);

namespace Empathy\MVC;

use DI\Container;
use DI\ContainerBuilder;
use Monolog\Logger;
use Spyc;

class DI
{
    /**
     * @var ContainerBuilder<Container>
     */
    private static ContainerBuilder $builder;
    private static Container $container;

    private static function loadConfig(string $configDir, ?Spyc $spyc = null): mixed
    {
        if (!$spyc instanceof \Spyc) {
            $spyc = new Spyc();
        }
        $configFile = $configDir.'/config.yml';
        return FileContentsCache::cachedCallback($configFile, function ($data) use (&$spyc) {
            return $spyc->YamlLoadString($data);
        });
    }

    private static function loadAdditional(string $location, string $docRoot = ''): void
    {
        if (file_exists($docRoot.$location)) {
            self::$builder->addDefinitions($docRoot.$location);
        }
    }

    public static function init(
        string $configDir,
        bool $persistentMode = false,
        bool $systemMode = false
    ): Container {
        self::$builder = new ContainerBuilder();
        self::$builder->addDefinitions([
            'configDir' => $configDir,
            'persistentMode' => $persistentMode,
            'systemMode' => $systemMode,
            'Spyc' => new Spyc(),
            'Empathy' => function (Container $c) {
                $loggingOn = (bool) $c->get('LoggingOn');
                $log = null;
                if ($loggingOn) {
                    $log = $c->get('Log');
                }

                return new Empathy(
                    $c->get('configDir'),
                    $c->get('persistentMode'),
                    $c->get('Config'),
                    $loggingOn,
                    $log
                );
            },
            'Bootstrap' => function (Container $c) {
                $empathy = $c->get('Empathy');
                $cache = $c->has('Cache') ? $c->get('Cache') : null;
                $cacheEnabled = $c->has('cacheEnabled') ? (bool) $c->get('cacheEnabled') : false;

                return new Bootstrap(
                    $empathy->getBootOptions(),
                    $empathy->getPlugins(),
                    $empathy,
                    $c->get('PluginManager'),
                    $c->get('Stash'),
                    $cache,
                    $cacheEnabled,
                    (bool) $c->get('ApcuDebug'),
                );
            },
            'URI' => function (Container $c) {
                return $c->get('Bootstrap')->createUri();
            },
            'ApcuDebug' => false,
            'PluginManager' => new PluginManager(),
            'Stash' => fn (Container $c) => new Stash(),
            'Config' => function (Container $c) {
                $diPath = realpath(__FILE__);
                if ($diPath === false) {
                    throw new Exception('Could not resolve path of DI.php');
                }

                return [
                    self::loadConfig($c->get('configDir')),
                    self::loadConfig(dirname($diPath).'/../../..'),
                ];
            },
            'LoggingOn' => false,
            'LoggingLevel' => Logger::DEBUG,
            'Log' => function (Container $c) {
                if (!$c->get('LoggingOn')) {
                    throw new Exception('Logging disabled');
                }
                $logging = new Logging($c->get('LoggingLevel'));
                return $logging->getLog();
            },
        ]);

        $appConfig = self::loadConfig($configDir, new Spyc());
        if (
            isset($appConfig['boot_options']['use_elib']) &&
            $appConfig['boot_options']['use_elib'] &&
            class_exists(\Empathy\ELib\Util\Libs::class)
        ) {
            $elibDirs = \Empathy\ELib\Util\Libs::findAll($appConfig['doc_root']);

            foreach ($elibDirs as $lib) {
                self::loadAdditional($lib . '/services.php', $appConfig['doc_root']);
            }
        }

        self::loadAdditional($configDir.'/services.php');
        self::$container = self::$builder->build();
        return self::$container;
    }

    public static function getContainer(): Container
    {
        return self::$container;
    }
}
