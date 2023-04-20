<?php

namespace Empathy\MVC;
use DI\Container;
use DI\ContainerBuilder;

class DI
{
    private static $builder;
    private static $container;

    private static function loadConfig($configDir, $spyc = null)
    {
        if ($spyc === null) {
            $spyc = DI::getContainer()->get('Spyc');
        }
        $configFile = $configDir.'/config.yml';
        return FileContentsCache::cachedCallback($configFile, function($data) use (&$spyc) {
            return $spyc->YamlLoadString($data);
        });
    }

    private static function loadAdditional($location, $docRoot = '')
    {
        if (file_exists($docRoot.$location)) {
            self::$builder->addDefinitions($docRoot.$location);
        }
    }

    public static function init(
        $configDir,
        $persistentMode = false,
        $systemMode = false
    ) {
        self::$builder = new ContainerBuilder();
        self::$builder->addDefinitions([
            'configDir' => $configDir,
            'persistentMode' => $persistentMode,
            'systemMode' => $systemMode,
            'Spyc' => new \Spyc(),
            'Empathy' => function (Container $c) {
                return new Empathy(
                    $c->get('configDir'),
                    $c->get('persistentMode'),
                    $c->get('systemMode')
                );
            },
            'Bootstrap' => function (Container $c) {
                $empathy = $c->get('Empathy');
                return new Bootstrap(
                    $empathy->getBootOptions(),
                    $empathy->getPlugins(),
                    $empathy
                );
            },
            'URI' => function (Container $c) {
                $bootstrap = $c->get('Bootstrap');
                return new URI(
                    $bootstrap->getDefaultModule(),
                    $bootstrap->getDynamicModule()
                );
            },
            'PluginManager' => new PluginManager(),
            'Stash' => new Stash(),
            'Config' => function (Container $c) {
                return [
                    self::loadConfig($c->get('configDir')),
                    self::loadConfig(dirname(realpath(__FILE__)).'/../../..')
                ];
            },
            'LoggingOn' => false,
            'Log' => function (Container $c) {
                if (!$c->get('LoggingOn')) {
                    throw new Exception('Logging disabled');
                }
                $logging = new Logging();
                return $logging->getLog();
            }
        ]);

        $appConfig = self::loadConfig($configDir, new \Spyc());
        if (
            isset($appConfig['boot_options']['use_elib']) &&
            $appConfig['boot_options']['use_elib']
        ) {
            $elibDirs = \Empathy\ELib\Util\Libs::findAll($appConfig['doc_root']);
            foreach ($elibDirs as $lib) {
                self::loadAdditional($lib.'/services.php', $appConfig['doc_root']);
            }            
        }

        self::loadAdditional($configDir.'/services.php');
        self::$container = self::$builder->build();
        return self::$container;
    }

    public static function getContainer()
    {
        return self::$container;
    }
}
