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

namespace Empathy;

class Bootstrap
{
  private $controller = null;
  private $module;
  private $moduleIsDynamic;
  private $specialised;
  private $uri;

  public function __construct($module, $moduleIsDynamic, $specialised)
  {
    $this->module = $module;
    $this->moduleIsDynamic = $moduleIsDynamic;
    $this->specialised = $specialised;
    spl_autoload_register('\Empathy\Bootstrap::loadClass');
    set_exception_handler('\Empathy\Bootstrap::exception_handler');
    if(USE_DOCTRINE == true)
      {
	require('Doctrine.php');
	spl_autoload_register(array('\Doctrine', 'autoload'));
      }
    if(USE_ZEND == true)
      {
	require('Zend/Loader.php');
	spl_autoload_register(array('\Zend_Loader', 'loadClass'));
      }
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

    try
      {
	$this->dispatch();
      }
    catch(Exception $e)
      {
	//echo $e->getMessage();exit();
       
	$this->controller = new Controller($this->uri->getError(), false);
	$this->controller->setTemplate('empathy.tpl');
	$this->controller->assign('error', $e->getMessage());
	$this->display(true);	
	
      }
  } 

  private function dispatch()
  {
    $this->uri = new URI($this->module, $this->moduleIsDynamic);
    $error = $this->uri->getError();
    if($error > 0)
      {
	throw new Exception('Some error trying to dispatch: '.$this->uri->getErrorMessage());
      }
    // attempting to leave in support for dsection
    if(in_array(1, $this->moduleIsDynamic))
      {
	if($u->getError() == MISSING_CLASS)
	  {
	    $u->dynamicSection($specialised);
	  }
      }
    $controller_name = $this->uri->getControllerName();
    $this->controller = new $controller_name($this->uri->getError(), false);     
    $this->controller->$_GET['event']();
    $this->display(false);
  }
  

  private function display($i)
  {
    if(PNG_OUTPUT == 1)
      {
	$this->controller->presenter->loadFilter('output', 'png_image');
      }
    $this->controller->initDisplay($i);
  }

  private function incPlugin($name)
  {
    require('empathy/include/plugin/empathy.'.$name.'.php');
  }

  public static function exception_handler($e)
  {
    // echo $e->getMessage()."\n";
  }
  

  public static function loadClass($class)
  {
    //echo $class."<br />\n";
    // $class = substr($class, 8); // hack around file structure
    // not matching namespaces
    $i = 0;
    $load_error = 1;

    //    echo $class."<br />";

    //    if(!strpos($class, '\\'))
    //    if(strpos($class, 'CustomController'))
    if(strpos($class, 'Controller\\')
       || strpos($class, 'Model\\'))
      {
	$class_arr = explode('\\', $class);
        $class = $class_arr[sizeof($class_arr)-1];
	$location = array(DOC_ROOT.'/application/',
			  DOC_ROOT.'/application/'.$_GET['module'].'/',
			  DOC_ROOT.'/storage/');	
      }
    else
      {
	$class = str_replace('\\', '/', $class);	
	$location = array('');
      }
    
    //    $location = array('empathy/include', DOC_ROOT.'/application',
    //	      'empathy/storage', DOC_ROOT.'/storage');

    while($i < sizeof($location) && $load_error == 1)
      {
	//$class_file = $location[$i].'/'.$class.'.php';           
	$class_file = $location[$i].$class.'.php';           

	if(@include($class_file))
	  {		
	    $class_file.": 1<br />\n";	
	    $load_error = 0;
	  }
	else
	  {
	    $class_file.": 0<br />\n";
	  }	
	$i++;
      }    

    
    /* old shizz
    $class = substr($class, 8);

    $i = 0;
    $load_error = 1;
    $location = array('empathy/include', DOC_ROOT.'/application',
		      'empathy/storage', DOC_ROOT.'/storage');
    
    while($i < sizeof($location) && $load_error == 1)
      {
	$class_file = $location[$i].'/'.$class.'.php';           
	if(@include($class_file))
	  {		

	    $load_error = 0;
	  }
	else
	  {

	  }	
	$i++;
      }
    */
  }

}
?>