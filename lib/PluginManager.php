<?php


namespace Empathy;

class PluginManager
{
  private $plugins;
  private $controller;
  private $view_plugin;
  

  public function __construct($c)
  {
    $this->controller = $c;
    $this->plugins = array();
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

  public function getView()
  {
    return $this->view_plugin;
  }
  


}
?>

