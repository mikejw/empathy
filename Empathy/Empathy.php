<?php
/**
 * Empathy 
 * @file			Empathy/Empathy.php
 * @description		Creates global object that initializes an Empathy application
 * @author			Mike Whiting
 * @license			LGPLv3
 *
 * (c) copyright Mike Whiting 
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */

const MVC_VERSION = '0.9.4';

require_once('spyc/spyc.php');

class Empathy
{

  /**
   * Boot object created before dispatch
   * @var Bootstrap
   */
  private $boot;


  /**
   * Boot options read from application config file.
   * @var array
   */
  private $bootOptions;


  /**
   * Plugin definition read from application config file.
   * @var array
   */
  private $plugins;

  
  /**
   * When application is set to handle errors
   * this array is used to collect the error messages.
   * @var array
   */
  private $errors;


  /**
   * Application persistent mode. Implies there could be multiple requests to handle
   * following initialization. This flag is passed directly to the application.
   * @var boolean
   */
  private $persistent_mode;

  
  /**
   * This flag is read from the boot_options section of the application config.
   * If it is true then the main autoload function will attempt to load ELib components
   * when necessary.
   * @var boolean
   */
  private $use_elib;



  /**
   * Create application object.
   * @param string $configDir the location of the application config file
   *
   * @param boolean $persistent_mode Whether the application is running in persistent mode.
   * If ture this means there could be many requests following initialization.
   * @return void
   */  
  public function __construct($configDir, $persistent_mode = null)
  {
    $this->persistent_mode = $persistent_mode;
    spl_autoload_register(array($this, 'loadClass'));
    $this->loadConfig($configDir);    
    $this->loadConfig(realpath(dirname(realpath(__FILE__)).'/../cfg/Empathy'));

    if(isset($this->bootOptions['use_elib']) &&
       $this->bootOptions['use_elib'])
      {
	$this->use_elib = true;
	\ELib\Config::load($configDir);
      }
    else
      {
	$this->use_elib = false;
      }

    if($this->getHandlingErrors())
      {
	set_error_handler(array($this, 'errorHandler'));    
      }

    $this->boot = new Empathy\Bootstrap($this->bootOptions, $this->plugins, $this);	
    
    $this->initPlugins();
    
    if($this->persistent_mode !== true)
      {
	$this->beginDispatch();
      }
  }


  private function getHandlingErrors()
  {
    return (isset($this->bootOptions['handle_errors']) &&
      $this->bootOptions['handle_errors']);
  }
  
  
  public function initPlugins()
  {
    if(!$this->getHandlingErrors())
      {
	$this->boot->initPlugins();
      }
    else
      {
	try
	  {
	    $this->boot->initPlugins();
	  }
	catch(\Exception $e)
	  {
	    $this->exceptionHandler($e);
	  }
      }
  }


  public function beginDispatch()
  {    
    if(!$this->getHandlingErrors())
      {
	$this->boot->dispatch();
      }
    else
      {
	try
	  {       
	    $this->boot->dispatch();
	  }        
	catch(\Exception $e)
	  {
	    $this->exceptionHandler($e);
	  }
      }
  }


  public function getPersistentMode()
  {
    return $this->persistent_mode;
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
	exit();
	break;

      default:
	// redispatch to error page 
	/*
	$_GET['module'] = 'notfound';
	$_GET['class'] = 'notfound';
	$_GET['event'] = 'default_event';
	$this->beginDispatch();
	*/

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

	if(isset($_GET['module']))
	  {
	    array_push($location, DOC_ROOT.'/application/'.$_GET['module'].'/');
	  }
	array_push($location, DOC_ROOT.'/storage/');
      }         
    elseif(strpos($class, 'Empathy') === 0 ||
	   (strpos($class, 'ELib') === 0 && $this->use_elib))
      {
	$class = str_replace('\\', '/', $class);	
      }
    array_push($location, DOC_ROOT.'/application/');

    
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