<?php

namespace Empathy\MVC\Plugin;
use Empathy\MVC\Plugin as Plugin;

class PresentationPlugin extends Plugin
{
	private $global;

	public function __construct($manager, $bootstrap, $config = null, $global = true)
	{
		parent::__construct($manager, $bootstrap, $config);
		$this->global = $global;
	}

	public function getGlobal()
	{
		return $this->global;
	}
}
