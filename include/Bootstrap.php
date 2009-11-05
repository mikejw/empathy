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

function my_spl_autoload($class)
{
  $i = 0;
  $load_error = 1;
  $location = array('empathy/include', DOC_ROOT.'/application',
		    'empathy/storage');
  //  if(!(isset($_SERVER['argc']) && $_SERVER['argc'] > 1))
  // {
      array_push($location, DOC_ROOT.'/storage');      
      //  }

  
  while($i < sizeof($location) && $load_error == 1)
    {
      $class_file = $location[$i].'/'.$class.'.php';           
      if(@include($class_file))
	{		
	  //echo $class_file;	
	  $load_error = 0;
	}
      else
	{
	  //echo $class_file;
	}	
      $i++;
    }


  /*
  
  if($load_error == 1 && USE_DOCTRINE == true)
    {
      if(@Doctrine::autoLoad($class))
	{
	  $load_error = 0;
	}
    }
  

  
  if($load_error == 1 && USE_ZEND == true)
    {
      if(!(@Zend_Loader::loadClass($class) == false))
	{
	  $load_error = 0;
	}
    }
  */
 
}

class Bootstrap
{
  private $controller = null;

  public function __construct($module, $moduleIsDynamic, $specialised)
  {

    
    if(USE_DOCTRINE == true)
      {
	require('Doctrine.php');
	//spl_autoload_register(array('Doctrine', 'autoload'));
      }
    if(USE_ZEND == true)
      {
	require('Zend/Loader.php');
	//spl_autoload_register(array('Zend_Loader', 'loadClass'));
      }
    
    spl_autoload_register('my_spl_autoload');

    $this->incPlugin('no_cache');
    #$this->incPlugin('force_www');
    #$this->incPlugin('force_endslash');

    array_push($module, 'empathy');
    array_push($moduleIsDynamic, 0);
    
    $u = new URI($module, $moduleIsDynamic);

    if(in_array(1, $moduleIsDynamic))
      {
	if($u->getError() == MISSING_CLASS)
	  {
	    $u->dynamicSection($specialised);
	  }
      }


    // dispatch    
    $controller_name = $u->getControllerName();

    //    echo $controller_name.
    //print_r($u);
    //exit();

    $this->controller = new $controller_name($u->getError(), $u->getInternal()); 
    


    $this->controller->$_GET['event']();


       
    if(PNG_OUTPUT == 1)
      {
	$this->controller->presenter->loadFilter('output', 'png_image');
      }
    $this->controller->initDisplay();
  } 

  private function incPlugin($name)
  {
    require('empathy/include/plugin/empathy.'.$name.'.php');
  }
}
?>