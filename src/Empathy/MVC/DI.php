<?php

namespace Empathy\MVC;


class DI {

	private static $container;

	public static function init(
		$configDir,
		$persistentMode = false,
		$systemMode = false
		) {

		$builder = new \DI\ContainerBuilder();
		$builder->addDefinitions([
		    
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
		    }
		]);
		self::$container = $builder->build();
		return self::$container;
	}

	public static function getContainer() {
		return self::$container;
	}

}