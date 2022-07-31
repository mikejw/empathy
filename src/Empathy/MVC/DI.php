<?php

namespace Empathy\MVC;

class DI
{
    private static $builder;
    private static $container;
    private static $useELib = false;

    private static function loadConfig($configDir, $spyc = null)
    {
        if ($spyc === null) {
            $spyc = DI::getContainer()->get('Spyc');
        }
        $configFile = $configDir.'/config.yml';
        if (!file_exists($configFile)) {
            throw new \Exception('Config error: '.$configFile.' does not exist');
        }
        return $spyc->YAMLLoad($configFile);
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
        self::$builder = new \DI\ContainerBuilder();
        self::$builder->addDefinitions([
            'configDir' => $configDir,
            'persistentMode' => $persistentMode,
            'systemMode' => $systemMode,
            'Spyc' => new \Spyc(),
            'Empathy' => function (\DI\Container $c) {
                return new Empathy(
                    $c->get('configDir'),
                    $c->get('persistentMode'),
                    $c->get('systemMode')
                );
            },
            'Bootstrap' => function (\DI\Container $c) {
                $empathy = $c->get('Empathy');
                return new Bootstrap(
                    $empathy->getBootOptions(),
                    $empathy->getPlugins(),
                    $empathy
                );
            },
            'URI' => function (\DI\Container $c) {
                $bootstrap = $c->get('Bootstrap');
                return new URI(
                    $bootstrap->getDefaultModule(),
                    $bootstrap->getDynamicModule()
                );
            },
            'PluginManager' => new PluginManager(),
            'Stash' => new Stash(),
            'Config' => function (\DI\Container $c) {
                return [
                    self::loadConfig($c->get('configDir')),
                    self::loadConfig(dirname(realpath(__FILE__)).'/../../../')
                ];
            }
        ]);

        $appConfig = self::loadConfig($configDir, new \Spyc());
        if ($appConfig['boot_options']['use_elib']) {
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
