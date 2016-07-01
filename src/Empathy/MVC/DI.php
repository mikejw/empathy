<?php

namespace Empathy\MVC;


class DI {



	public static function init() {


		global $configDir, $persistentMode, $systemMode, $container;


		$builder = new \DI\ContainerBuilder();
		$builder->addDefinitions([
		    
		    'configDir' => $configDir,
		    'persistentMode' => isset($persistentMode)? $persistentMode: null,
		    'systemMode' => isset($systemMode)? $systemMode: null,
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
		return $builder->build();
	}
}