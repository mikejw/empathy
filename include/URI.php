<?php
  // Copyright 2008 Mike Whiting (mail@mikejw.co.uk).
  // This file is part of the Empathy MVC framework.

  // Empathy is free software: you can redistribute it and/or modify
  // it under the terms of the GNU Lesser General Public License as published by
  // the Free Software Foundation, either version 3 of the License, or
  // (at your option) any later version.

  // Empathy is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU Lesser General Public License for more details.

  // You should have received a copy of the GNU Lesser General Public License
  // along with Empathy.  If not, see <http://www.gnu.org/licenses/>.

define('MISSING_CLASS', 1);
define('MISSING_CLASS_DEF', 2);
define('MISSING_EVENT_DEF', 3);
define('MAX_COMP', 4); // maxium relevant info stored in a URI
                       // ie module, class, event, id

class URI
{
  private $full;
  private $uriString;
  private $uri;
  private $module;
  private $moduleIsDynamic;

  public $error;
  public $internal = false;
  public $controllerPath = '';
  public $controllerName = '';


  public function __construct($module, $moduleIsDynamic)
  {
    $removeLength = strlen(WEB_ROOT.PUBLIC_DIR);

    $this->module = $module;
    $this->moduleIsDynamic;
    $this->full = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $this->uriString = substr($this->full, $removeLength + 1);
    $this->error = 0;
  }

  public function printRoute()
  {
    echo $_GET['module'].'<br />';
    echo $_GET['class'].'<br />';
    echo $_GET['event'].'<br />';
    echo $this->error;
    exit();
  }

  public function doStuff()
  {   
    $this->processRequest();

    $this->assertClassIsSet();

    $this->doOtherStuff();

    $this->assertEventIsSet();
  }

  public function processRequest()
  {        
    if(isset($_GET['module']))
      {
	$this->setModule($_GET['module']); 	
      }
    
    if($this->uriString == '')
      {	
	$this->setModule($this->module[DEF_MOD]);
      }
    else
      {
	$this->formURI();
	if(!$this->detectVars())
	  {
	    $this->analyzeURI();
	  }
      }
  }

  public function formURI()
  {
    $uri = explode('/', $this->uriString);
    // remove empty element caused by trailing slash
    if($uri[(sizeof($uri) -1)] == '')
      {
	array_pop($uri);
      }
    $this->uri = $uri;
  }

  public function detectVars()
  {
    $vars = false;
    $i = 0;
    while($vars == false && $i < sizeof($this->uri))
      {	
	if(eregi('=', $this->uri[$i]))
	  {
	    $vars = true; 
	  }
	$i++;
      }
    return $vars;
  }


  public function analyzeURI()
  {    
    $modIndex = 0;
    $completed = 0;
    $j = 0;
    $i = 0;
    $current = '';

    $length = sizeof($this->uri);
    if($length > MAX_COMP)
      {
	$length = MAX_COMP;
      }
	 
    while($i < $length)
      { 
	$current = $this->uri[$i];
	
	if(!isset($_GET['id']) && is_numeric($current))
	  {
	    $_GET['id'] = $current;
	    $i++;
	    continue;
	  }      
	
	if(!isset($_GET['module']))
	  {
	    while($j < sizeof($this->module) && $current != $this->module[$j])
	      {
		$j++;
	      }
	    $modIndex = $j;
	    
	    // current is not a module so set to default
	    if($modIndex == sizeof($this->module))
	      {
		$modIndex = DEF_MOD;
		//$this->error = MISSING_CLASS;
	      }
	    $this->setModule($this->module[$modIndex]);
	    $i++;
	    continue;
	  }
	
	if(!isset($_GET['class']))
	  {
	    $class_error = $this->invalidClass($current);
	    if($class_error)
	      {
		$this->error = $class_error;
		$_GET['class'] = $_GET['module'];	  
	      }
	    else
	      {
		$_GET['class'] = $current;
	      }
	    $i++;
	    continue;
	  }

	if(!isset($_GET['event']))
	  {
	    $_GET['event'] = $current;
	  }   
	$i++;
      }
  }        

  public function doOtherStuff()
  {
    $this->controllerName = $_GET['class'];
    if(!class_exists($this->controllerName))
      {       
	if(!$this->internal)
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
	    if(!$u->internal)
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
		$this->error = MISSING_CLASS;
	      }
	  }
      }
           
    if(!class_exists($this->controllerName) && $this->error == 0)
      {
	$this->error = MISSING_CLASS_DEF; 
	$this->controllerPath = 'empathy/include/CustomController.php';
	$this->controllerName = 'CustomController';
      }
    }
    

  public function dynamicModule()
  {
    $moduleIndex = 0;

    if((!(isset($_GET['module']))) || (!(in_array($_GET['module'], $this->module))))
      {
	$_GET['module'] = $this->module[$moduleIndex];
      }
    else
      {
	$i = 0;
	while($_GET['module'] != $this->module[$i])
	  {
	    $i++;
	  }
	$moduleIndex = $i;
      }

    if($this->moduleIsDynamic[$moduleIndex])
      {
	$c = new Controller();
	$section = new SectionItem($c);
	$section->module = $_GET['module'];
	$this->controllerName = $this->dynamicSection($section, $uri);
	$this->controllerPath = DOC_ROOT."/application/default/$controllerName.php";
      } 
  }



  private function dynamicSection($section, $uri)
  {
    // code to assert correct section path - else throw 404
    
    if(sizeof($uri) > 0)
      {
	$section_index = (sizeof($uri) - 1);
	if(is_numeric($uri[$section_index]))
	  {
	    $_GET['id'] = $uri[$section_index--];
	  }
	$section_uri = $uri[$section_index];		       
      }
    
    if(!(isset($section_uri)))
      {
	$section_uri = "home";
      }  
    
    $rows = $section->getURIData();
    for($i = 0; $i < sizeof($rows); $i++)
      {
	if($rows[$i]['friendly_url'] != NULL)
	  {
	    $comp = str_replace(" ", "", strtolower($rows[$i]['friendly_url']));
	  }
	else
	  {
	    $comp = str_replace(" ", "", strtolower($rows[$i]['label']));
	  }
	if($comp == $section_uri)
	  {
	    $_GET['section'] = $rows[$i]['id'];
	  }
      }
    
    if(isset($_GET['section']))
      {
	$section->getItem($_GET['section']);
      }
    
    // section id is not set / found
    if(!(is_numeric($section->id)))
      {
	header("Location: http://".WEB_ROOT);
	exit();
      }
    
    if(isset($_GET['id']))
      {
	$section->layout = REGULAR_LAYOUT;
      }
    
    if($section->layout == "")
      {
	$end_uri = $section->buildURL($section->getFirstChild($section->id));
	$j = (sizeof($end_uri) - 1);
	$k = 0;
	$full_url = "http://".WEB_ROOT."/";
	while($j >= $k)
	  {
	    $full_url .= str_replace(" ", "", strtolower($end_uri[$j]))."/";
	    $j--;    
	  }
	header("Location: $full_url");
	exit();
      }
    else
      {
	$specialised = array();
	
	if(in_array($section->id, $specialised))
	  {
	    $controllerName = "layout".$section->id;
	  }
	else
	  {
	    $controllerName = "layout".$section->layout;
	  }   
      }
    return $controllerName;
  }
  
  
  private function setModule($module)
  {
    $_GET['module'] = $module;
    if($_GET['module'] == 'empathy')
      {
	$this->internal = true;
      }
  }
  

  private function invalidClass($class)
  {
    $class_error = 0;
    if(!$this->internal)
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
	$class_error = MISSING_CLASS;
      }
    else
      {
	require($classPath);
	if(!class_exists($class))
	  {
	    $class_error = MISSING_CLASS_DEF;
	  }
      }  
    return $class_error;
  }

  public function assertClassIsSet()
  {
    if(!(isset($_GET['class'])))
      {
	$_GET['class'] = $_GET['module'];
      }
  }

  public function assertEventIsSet()
  {
    if(!(isset($_GET['event'])))
      {
	$_GET['event'] = 'default_event';
      }       
  }

}
?>