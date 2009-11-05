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
define('ERROR_404', 4);
define('MAX_COMP', 4); // maxium relevant info stored in a URI
                       // ie module, class, event, id

class URI
{
  private $full;
  private $uriString;
  private $uri;
  private $module;
  private $moduleIsDynamic;
  private $error;
  private $internal = false;
  private $controllerPath = '';
  private $controllerName = '';

  public function __construct($module, $moduleIsDynamic)
  {
    $removeLength = strlen(WEB_ROOT.PUBLIC_DIR);

    $this->module = $module;
    $this->moduleIsDynamic = $moduleIsDynamic;
    $this->full = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $this->uriString = substr($this->full, $removeLength + 1);
    $this->error = 0;

    //    echo $this->uriString;

    $this->processRequest();
    $this->setController(); 
    //$this->printRouting();
  }

  public function getError()
  {
    return $this->error;
  }

  public function getControllerName()
  {
    return $this->controllerName;
  }

  public function getInternal()
  {
    return $this->internal;
  }

  public function printRouting()
  {
    echo "<pre>\n";
    echo "Module:\t\t\t".$_GET['module']."\n";
    echo "Class:\t\t\t".$_GET['class']."\n";
    echo "Event:\t\t\t".$_GET['event']."\n\n";   
    echo "Controller Path:\t".$this->controllerPath."\n";
    echo "Controller Name:\t".$this->controllerName."\n";
    echo "Error:\t\t\t".$this->error."\n</pre>";
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
	// detectVars is now obsolete
	// will attempt to find correct route when args are present
	/*
	if(!$this->detectVars())
	  {
	    $this->analyzeURI();
	  }	
	*/
	$this->analyzeURI();
      }
  }

  public function formURI()
  {
    $uri = explode('/', $this->uriString);
    $size = sizeof($uri) - 1; 
    // remove empty element caused by trailing slash
    if($uri[$size] == '')
      {
	array_pop($uri);
	$size--;
      }
    // ignore any args
    if(preg_match('/\?/', $uri[$size]))
      {
	$start_args = strpos($uri[$size], '?');
	$uri[$size] = substr($uri[$size], 0, $start_args);	    
	if($uri[$size] == '')
	  {
	    array_pop($uri);
	  }      
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
		$_GET['class'] = $current;

		$this->error = MISSING_CLASS;
	      }
	    $this->setModule($this->module[$modIndex]);
	    $i++;
	    continue;
	  }
	
	if(!isset($_GET['class']))
	  {	    
	    $_GET['class'] = $current;
	    $i++;
	    continue;
	  }

	if(!isset($_GET['event']))
	  {
	    $_GET['event'] = $current;
	  }   
	$i++;
      }
    if(!isset($_GET['module']))
      {	
	// only url param is an id
	$this->setModule($this->module[DEF_MOD]);
	$this->error = MISSING_CLASS;
      }
  }        
   
  private function setModule($module)
  {
    $_GET['module'] = $module;
    if($_GET['module'] == 'empathy')
      {
	$this->internal = true;
      }
  }
  
  public function setControllerPath()
  {
    if(!$this->internal)
      {
	$this->controllerPath = DOC_ROOT.'/application/'.$_GET['module'].'/'.$_GET['class'].'.php';
      }
    else
      {
	$pathToEmp = explode('empathy', __FILE__);	
	$this->controllerPath = $pathToEmp[0].'empathy/application/'.$_GET['module'].'/'.$_GET['class'].'.php';
      }
  }

  private function setController()
  {      
    if(!(isset($_GET['class'])) && isset($_GET['module']))
      {
	$_GET['class'] = $_GET['module'];
      }

    if(isset($_GET['class']))
      {
	$this->controllerName = $_GET['class'];
	$this->setControllerPath();
      }
   
    if(!is_file($this->controllerPath))
      {
	if(isset($_GET['class']))
	  {
	    $_GET['event'] = $_GET['class'];
	  }

	// module must be set?
	if(isset($_GET['module']))
	  {
	    $_GET['class'] = $_GET['module'];
	    $this->controllerName = $_GET['module'];
	    $this->setControllerPath();
	  }
	if(!is_file($this->controllerPath))
	  {
	    $this->error = MISSING_CLASS;
	  }
      }
 

    if(!$this->error)
      {
	require($this->controllerPath);		
	if(!class_exists($this->controllerName))
	  {
	    $this->error = MISSING_CLASS_DEF;
	  }
      }	  

    $this->assertEventIsSet();   

    if(!$this->error)
      {	
	$r = new ReflectionClass($this->controllerName);
	if(!$r->hasMethod($_GET['event']))	       
	  {
	    $this->error = MISSING_EVENT_DEF;
	  }        
      }

    if($this->error)
      {
	$this->controllerPath = 'empathy/include/CustomController.php';
	$this->controllerName = 'CustomController';
      }
  }

  public function assertEventIsSet()
  {
    if(!(isset($_GET['event'])) || $_GET['event'] == '')
      {
	$_GET['event'] = 'default_event';
      }       
  }

  public function dynamicSection($specialised = array())
  {
    // code to assert correct section path - else throw 404
   
    $section = new SectionItemStandAlone();

    // find dynamic module
    // needs error handling when dynamic module does not exist or is not set
    $i = 0;
    while($this->moduleIsDynamic[$i] == 0)
      {
	$i++;
	if($i > sizeof($this->moduleIsDynamic) - 1)
	  {
	    die("Reference to a dynamic module was searched for but not found.");
	  }
      }    
    $_GET['module'] = $this->module[$i];

    if(sizeof($this->uri) > 0)
      {
	$section_index = (sizeof($this->uri) - 1);
	if(is_numeric($this->uri[$section_index]))
	  {
	    $_GET['id'] = $this->uri[$section_index--];
	  }
	$section_uri = $this->uri[$section_index];		       
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
	//	header("Location: http://".WEB_ROOT);
	//exit();
	$this->error = ERROR_404;
      }
    
    if(isset($_GET['id']))
      {
	$section->template = REGULAR_LAYOUT;
      }
    
    if(isset($section->url_name))
      {
	$_GET['section_uri'] = $section->url_name;
      }

    if($section->template == "")
      {
	//echo 'no template.';
	//exit();	
      }
    else
      {	
	if(in_array($section->id, $specialised))
	  {
	    $controllerName = "template".$section->id;
	  }
	else
	  {
	    $controllerName = "template".$section->template;
	  }   
      }
    
    if(isset($controllerName))
      {
	$_GET['class'] = $controllerName;
	$this->error = 0;
      }

    $_GET['event'] = 'default_event';
    $_GET['id'] = $section->id;

    $this->setController();
  }


}
?>