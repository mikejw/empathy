<?php

class Bootstrap
{
  private $internalController = false;
  private $controllerPath = '';
  private $controllerName = '';
  private $controller = null;
  private $controllerError = 0;

  public function __construct($module, $moduleIsDynamic)
  {
    #incPlugin('no_cache');

    array_push($module, 'empathy');
    array_push($moduleIsDynamic, 0);

    require(DOC_ROOT.'/application/CustomController.php');
    require('empathy/include/SmartyPresenter.php');    
    
    if(isset($_GET['module']))
      {
	$this->setModule($_GET['module']); 	
      }
    else
      {
	$this->processRequest($module);
      }       

    if(!(isset($_GET['class'])))
      {
	$_GET['class'] = $_GET['module'];
      }
    
    $this->controllerName = $_GET['class'];
    if(!class_exists($this->controllerName))
      {       
	if(!$this->internalController)
	  {
	    $this->controllerPath = DOC_ROOT.'/application/'.$_GET['module'].'/'.$this->controllerName.'.php';
	  }
	else
	  {
	    $this->controllerPath = 'empathy/application/'.$_GET['module'].'/'.$this->controllerName.'.php';
	  }
	
	$inc = @require($this->controllerPath); // attempt to include controller   
	
	if($inc != 1) // try again supposing the module has not been specified in the url
	  {	   
	    $_GET['event'] = $_GET['class'];
	    $_GET['class'] = $_GET['module'];
	    $this->controllerName = $_GET['module'];	
	    if(!$this->internalController)
	      {
		$this->controllerPath = DOC_ROOT.'/application/'.$_GET['module'].'/$controllerName.php';
	      }
	    else
	      {
		$this->controllerPath = 'empathy/application/'.$_GET['module'].'/$controllerName.php';
	      }
	    $inc2 = @require($this->controllerPath);
	    if($inc == 1 || $inc2 == 1)
	      {
		$this->controllerError = 1;
	      }
	  }
      }
           
    if(!class_exists($this->controllerName) && $this->controllerError == 0)
      {
	$this->controllerError = 2; 
	$this->controllerPath = 'empathy/include/CustomController.php';
	$this->controllerName = 'CustomController';
      }
      
    if(!(isset($_GET['event'])))
      {
	$_GET['event'] = 'default_event';
      }       
    
    $this->controller = new $this->controllerName($this->controllerError, $this->internalController, 0);
    $this->controller->$_GET['event']();
    
    //$presenter->smarty->load_filter('output', 'png_image');

    $this->controller->initDisplay();
  } 

  private function incPlugin($name)
  {
    require('empathy/include/plugin/empathy.'.$name.'.php');
  }
  
  private function setEvent($event)
  {
    $_GET['event'] = $event;
  }
  

  private function setModule($module)
  {
    $_GET['module'] = $module;
    if($_GET['module'] == 'empathy')
      {
	$this->internalController = 1;
      }
  }
  
  private function setClass($class)
  {
    $_GET['class'] = $class;
  }

  private function invalidClass($class)
  {
    $error = 0;
    if(!$this->internalController)
      {
	$classPath = DOC_ROOT.'/application/'.$_GET['module'].'/'.$class.'.php';
      }
    else
      {
	$pathToEmp = explode('empathy', __FILE__);	
	$classPath = $pathToEmp[0].'empathy/application/'.$_GET['module'].'/'.$class.'.php';
      }
   
    if(!is_file($classPath))
      {
	$error++;
      }
    else
      {
	require($classPath);
	if(!class_exists($class))
	  {
	    $error++;
	  }
      }  
    return $error;
  }


  private function processRequest($module)
  {    
    #$this->incPlugin('force_www');
    $this->incPlugin('force_endslash');
    
    $fullURI = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $removeLength = strlen(WEB_ROOT.PUBLIC_DIR);
    $uriString = substr($fullURI, $removeLength + 1);
    $errorCode = 0;

    if($uriString == '')
      {
	$this->setModule($module[DEF_MOD]);
      }
    else
      {	
	$uri = explode('/', $uriString);
	if($uri[(sizeof($uri) -1)] == '')
	  {
	    array_pop($uri);
	  }
	$completed = 0;
	$j = 0;
	$skip = 0;
	$modIndex = 0;
	for($i = 0; ($i < sizeof($uri) && $i < 4); $i++)
	  {
	    $skip = 0;
	    
	    if(eregi('=', $uri[$i]))
	      {
		$skip = 1;
	      }
	    
	    if(is_numeric($uri[$i]) && !isset($_GET['id']))
	      {
		$_GET['id'] = $uri[$i];
		$skip = 1;
	      }      
	    
	    if(!isset($_GET['module']) && $skip == 0)
	      {
		while($j < sizeof($module) && $uri[$i] != $module[$j])
		  {
		    $j++;
		  }
		$modIndex = $j;
		if($modIndex == sizeof($module))
		  {
		    $modIndex = DEF_MOD;
		    $errorCode = 1; // module not found
		  }
		else
		  {
		    $skip = 1;
		  }
		$this->setModule($module[$modIndex]);
	      }
	    if(!isset($_GET['class']) && $skip == 0)
	      {
		if($this->invalidClass($uri[$i]))
		  {
		    $errorCode = 2; // class error
		    $this->setClass($_GET['module']);	  
		  }
		else
		  {
		    $this->setClass($uri[$i]);
		    $skip = 1;
		  }
	      }
	    if(!isset($_GET['event']) && $skip == 0)
	      {
		$this->setEvent($uri[$i]);
	      }   
	  }  
      }
  }
}
?>