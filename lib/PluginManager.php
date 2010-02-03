<?php


namespace Empathy;

class PluginManager
{
  private $plugins;
  private $controller;
  private $view_plugin;
  private $initialised;
  

  public function __construct()
  {
    $this->initialised = 0;   
    $this->plugins = array();
    $this->view_plugin = NULL;
  }
  
  public function init($c)
  {
    $this->initialised = 1;
    $this->controller = $c;
  }

  public function register($p)
  {
    $this->plugins[] = $p;
  }

  public function preDispatch()
  {
    foreach($this->plugins as $p)
      {
	$r = new \ReflectionClass(get_class($p));	
	if(in_array('Empathy\Plugin\PreDispatch', $r->getInterfaceNames()))
	  {
	    $p->onPreDispatch($this->controller);
	  }
	if(in_array('Empathy\Plugin\Presentation', $r->getInterfaceNames()))
	  {
	    $this->view_plugin = $p;
	  }
      }
  }

  public function getInitialised()
  {
    return $this->initialised;
  }

  public function getView()
  {
    if($this->view_plugin === NULL)
      {
	throw new \Exception('No plugin loaded for view.');
      }
    else
      {
	return $this->view_plugin;
      }
  }
  


}
?>

