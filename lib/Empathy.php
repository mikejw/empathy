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

use Empathy as Empathy;

const MVC_VERSION = '1.0';

require('spyc/spyc.php');

class Empathy
{
  private $boot;
  private $bootOptions;

  public function __construct($configDir)
  {
    date_default_timezone_set('Europe/London');
    spl_autoload_register(array($this, 'loadClass'));

    $this->loadConfig($configDir);    
    $this->loadConfig(realpath(dirname(realpath(__FILE__)).'/../config'));

    $this->boot = new Empathy\Bootstrap($this->bootOptions);
    try
      {
	$this->boot->dispatch();
      }
    catch(Exception $e)
      {
	$this->exceptionHandler($e);
      }
  }

  private function exceptionHandler($e)
  {
    switch(get_class($e))
      {
      case 'Empathy\SafeException':
	echo $e->getMessage();
	break;
      default:
	$this->boot->dispatchException($e);
	break;
      }
  }

  private function loadConfig($configDir)
  {
    $s = new \Spyc();
    $config = $s->YAMLLoad($configDir.'/config.yml');      
    foreach($config as $index => $item)
      {
	if(!is_array($item))
	  {
	    define(strtoupper($index), $item);
	  }
      }   
    if(isset($config['boot_options']))
      {
	$this->bootOptions = $config['boot_options'];
      }
    if(isset($config['dependency']))
      {
	foreach($config['dependency'] as $item)
	  {
	    require($item['path']);
	    spl_autoload_register(array($item['class'], $item['loader']));
	  }
      }
  }


  public static function loadClass($class)
  {
    $i = 0;
    $load_error = 1;
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
    
    while($i < sizeof($location) && $load_error == 1)
      {
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
  }
}
?>