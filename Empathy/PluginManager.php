<?php


namespace Empathy;

class PluginManager
{
  private $plugins;
  private $view_plugins;
  private $initialised;
  private $controller;
  

  public function __construct()
  {
    $this->initialised = 0;   
    $this->plugins = array();
    $this->view_plugins = array();
  }

  
  public function setController($c)
  {
    $this->controller = $c;
  }

  
  public function init()
  {
    $this->initialised = 1;
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
	    $p->onPreDispatch();
	  }
	if(in_array('Empathy\Plugin\Presentation', $r->getInterfaceNames()))
	  {	    
	    $this->view_plugins[] = $p;
	  }
      }
  }

  public function getInitialised()
  {
    return $this->initialised;
  }

  public function getView()
  {
    if(sizeof($this->view_plugins) == 0)
      {
	throw new \Exception('No plugin loaded for view.');
      }
    else
      {
	$module = $this->controller->getModule();
	$class = $this->controller->getClass();

	$plugin = 0;
	if($module == 'api')
	  {
	    $plugin = 1;
	  }

	$view = $this->view_plugins[$plugin];
	
	return $view;	
      }
  }
  


}
?>