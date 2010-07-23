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

const MVC_VERSION = '0.9.4';

require('spyc/spyc.php');

class Empathy
{
  private $boot;
  private $bootOptions;
  private $plugins;
  private $errors;
  private static $elib;

  public function __construct($configDir, $override = null)
  {
    spl_autoload_register(array($this, 'loadClass'));
    set_error_handler(array($this, 'errorHandler'));    
    $this->loadConfig($configDir);    
    $this->loadConfig(realpath(dirname(realpath(__FILE__)).'/../config'));
    if(isset($this->bootOptions['use_elib']) &&
       $this->bootOptions['use_elib'])
      {
	self::$elib = true;
      }
    else
      {
	self::$elib = false;
      }

    if($override !== true)
      {
	$this->beginDispatch();
      }
  }

  public function beginDispatch()
  {    
    $this->boot = new Empathy\Bootstrap($this->bootOptions, $this->plugins, $this);	
    try
      {
	$this->boot->dispatch();
      }        
    catch(\Exception $e)
      {
	$this->exceptionHandler($e);
      }    
  }

  public function getErrors()
  {
    return $this->errors;
  }

  public function hasErrors()
  {
    return (sizeof($this->errors) > 0);
  }

  public function errorsToString()
  {
    return implode('</h2><h2>&nbsp;</h2><h2>', $this->getErrors());
  }


  public function errorHandler($errno, $errstr, $errfile, $errline)
  {  
    if(error_reporting())
      {
	$msg = '';
	switch ($errno)
	  {
	  case E_ERROR:	
	  case E_USER_ERROR:
	    $msg = "Error: [$errno] $errstr";
	    $msg .= "  Fatal error on line $errline in file $errfile";
	    $msg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")";
	    $msg .= " Aborting...";
	    die($msg);
	    break;	
	  case E_WARNING:
	  case E_USER_WARNING:
	    $msg = "Warning: [$errno] $errstr";
	    break;	    
	  case E_NOTICE:
	  case E_USER_NOTICE:
	    $msg = "Notice: [$errno] $errstr";
	    break;	           
	  case E_DEPRECATED:
	  case E_STRICT:
	    $msg = "Strict/Deprecated notice: [$errno] $errstr";	
	    break;
	  default:
	    $msg = "Unknown error type: [$errno] $errstr";
	    break;
	  }
	$msg .= " on line $errline in file $errfile";
	//$msg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")";
	$this->errors[] = $msg;
      }
    return true;
  }

  private function exceptionHandler($e)
  {
    if($this->hasErrors())
      {
	$e = new ErrorException($this->errorsToString());
      }
   
    // force safe exception
    //$e = new Empathy\SafeException($e->getMessage());

    switch(get_class($e))
      {
      case 'Empathy\SafeException':
	echo 'Safe exception: '.$e->getMessage();
	break;
      default:
	$this->boot->dispatchException($e);
	break;
      }
  }

  private function loadConfig($configDir)
  {
    $configFile = $configDir.'/config.yml';
    if(!file_exists($configFile))
      {
	die('Config error: '.$configFile.' does not exist');
      }
    $s = new \Spyc();
    $config = $s->YAMLLoad($configFile);      
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

    if(isset($config['plugins']))
      {
	$this->plugins = $config['plugins'];
      }
  }


  public static function loadClass($class)
  {
    $i = 0;
    $load_error = 1;
    $location = array('');
    if(strpos($class, 'Controller\\')
       || strpos($class, 'Model\\'))
      {
	$class_arr = explode('\\', $class);
        $class = $class_arr[sizeof($class_arr)-1];
	$location = array(
			  DOC_ROOT.'/application/',
			  DOC_ROOT.'/application/'.$_GET['module'].'/',
			  DOC_ROOT.'/storage/');	
      }         
    elseif(strpos($class, 'Empathy') === 0 ||
	   (strpos($class, 'ELib') === 0 && self::$elib))
      {
	$class = str_replace('\\', '/', $class);	
      }
    
    while($i < sizeof($location) && $load_error == 1)
      {
	$class_file = $location[$i].$class.'.php';           

	//echo $class_file.'<br />';

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