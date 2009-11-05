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

class Controller
{
  protected $module;
  protected $class;
  protected $event;
  protected $templateFile;
  protected $title;
  public $presenter;
  public $connected;
  private $initError;
  private $internal;
  protected $d_man;
  protected $d_conn;
  
  public function __construct($error, $i)
  {
    $this->initError = $error;
    $this->internal = $i;
    $this->connected = false;
    $this->module = $_GET['module'];
    $this->class = $_GET['class'];
    $this->event = $_GET['event'];
    $this->title = TITLE;
    $this->presenter = new SmartyPresenter($i);
    
    if(TPL_BY_CLASS == 0)
      {
	$this->templateFile = $this->module.'.tpl';
      }
    else
      {
	$this->templateFile = $this->class.'.tpl';
      }

    $this->sessionUp();
		
    $message = '';
    switch($this->initError)
      {
      case 1:
	$message = 'Missing class file.';
	break;
      case 2:
	$message = 'Missing class definition.';
	break;
      case 3:	    
	$message = 'Controller event '.$this->event.' has not been defined.';	    
	break;
      default:
	break;     
      }
	
    if($this->initError != 0)
      {
	$this->error($message);
      }   
    
    $this->assignSessionVar();
	
    $this->presenter->assign('module', $this->module);
    $this->presenter->assign('class', $this->class);
    $this->presenter->assign('event', $this->event);
    $this->presenter->assign('TITLE', TITLE);	
    
    if(isset($_GET['section_uri']))
      {
	$this->presenter->assign('section', $_GET['section_uri']);
      }      

    // doctrine stuff    
    /*
    if(USE_DOCTRINE == true)
      {
	$this->d_man = Doctrine_Manager::getInstance();
	$dsn = 'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_SERVER.'/'.DB_NAME;
	$this->d_conn = Doctrine_Manager::connection($dsn, 'c_'.NAME);
	$this->d_man->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
	$this->d_man->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL);
	$this->d_man->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
	$this->d_man->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

	// doctrine operations	       
	if(isset($_SERVER['argc']) && $_SERVER['argc'] > 1)
	  {
	    switch($_SERVER['argv'][1])
	      {
	      case 'doctrine_models':
		Doctrine::generateModelsFromDb(DOC_ROOT.'/models', array('c_'.NAME), array('generateTableClasses' => true
											   //, 'suffix' => 'DM.php'
											   ));
		exit(1);
		break;
	      case 'doctrine_yaml':
		Doctrine::generateYamlFromModels(DOC_ROOT.'/schema.yml', DOC_ROOT.'/models');
		exit(1);
		break;
	      case 'doctrine_generate':
		Doctrine::dropDatabases();
		Doctrine::createDatabases();
		Doctrine::generateModelsFromYaml(DOC_ROOT.'/schema.yml', DOC_ROOT.'/models');
		Doctrine::createTablesFromModels(DOC_ROOT.'/models');		
		exit(1);
		break;
	      default:
		die('No valid command line operation specified.'."\n");
		break;
	      }	    
	  }
	else
	  {
	    Doctrine::loadModels(DOC_ROOT.'/models');	
	  }
      }    
    */
  }

 
  public function initDisplay()
  {
    if(!$this->presenter->templateExists($this->templateFile))
      {
	$this->error('Missing template file: '.$this->templateFile);
      }
    else
      {	
	$this->presenter->display($this->templateFile);   
      }
  }
  
  public function assignSessionVar()
  {
    foreach($_SESSION as $index => $value)
    {
      $this->presenter->assign($index, $value);
    }
  }
   
  public function redirect($endString)
  {
    session_write_close();    
    $location = 'Location: ';
    $location .= 'http://'.WEB_ROOT.PUBLIC_DIR.'/';
    if($endString != '')
      {
	$location .= $endString;
      }
    header($location);
    exit();
  }

  public function redirect_cgi($endString)
  {
    session_write_close();    
    $location = 'Location: ';
    $location .= 'http://'.CGI.'/';
    if($endString != '')
      {
	$location .= $endString;
      }
    header($location);
    exit();
  }
  
  public function sessionUp()
  {    
    @session_start();
  }
  
  public function sessionDown()
  {
    session_unset();
    session_destroy();
  }

  public function toggleEditMode()
  {
    if($_SESSION['edit_mode'] != 1)
    {
      $_SESSION['edit_mode'] = 1;      
    }
    else
    {
      $_SESSION['edit_mode'] = 0;
    }
  }
  
  protected function setFailedURI($uri)
  {
    $_SESSION['failed_uri'] = $uri;
  }


  public function http_error($type)
  {
    $response = '';
    $message = '';
    switch($type)
      {
      case 400:
	$response = 'HTTP/1.1 400 bad request';
	$message = 'Bad request';
	break;
      case 404:
	$response = 'HTTP/1.0 404 Not Found';
	$message = 'Sorry this page does not exist or is out of date.';
	break;	
      }
    header($response);
    $this->presenter->assign('message', $message);
    $this->presenter->assign('code', $type);
    $this->presenter->display('http_error.tpl');
    exit();
  }


  public function error($message)
  {    
    if(DEBUG_MODE == 0)
      {
	$this->http_error(404);
      }
    else
      {
	$this->setFailedURI($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	$_SESSION['app_error'] = array($this->module, $this->class, $message, date('U'));
	$this->redirect('empathy/error/');       
      }
  }

  public function loadUIVars($ui, $ui_array)
  {
    foreach($ui_array as $setting)
      {
	if(isset($_GET[$setting]))
	  {
	    $_SESSION[$ui][$setting] = $_GET[$setting];	    
	  }
	elseif(isset($_SESSION[$ui][$setting]))
	  {
	    $_GET[$setting] = $_SESSION[$ui][$setting];
	  }
      }
  }

  public function isXMLHttpRequest()
  {
    $request = 0;
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
       ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
      {
	$request = 1;
      }
    return $request;
  }

  public function initID($id, $def, $assertSet=false) // when $def is 0, valid is true when id is 0
  {
    $valid = true;
    $assign_def = false;

    if(!isset($_GET[$id]))
      {
	$assign_def = true;
	if($assertSet)
	  {
	    $valid = false;
	  }
      }
    elseif(!((string) $_GET[$id] === (string)(int) $_GET[$id]) || ($_GET[$id] == 0 && $def != 0)
	   || $_GET[$id] < 0)
      {
	$assign_def = true;
	$valid = false;
      }

    if($assign_def)
      {
	$_GET[$id] = $def;
      }

    return $valid;
  }


  public function execScript($script, $args)
  {    
    $exec = 'cd '.DOC_ROOT.'/scripts; ';
    $exec .= PERL.' '.DOC_ROOT.'/scripts/'.$script.' '.implode(' ', $args);
    exec($exec);
  }
}
?>