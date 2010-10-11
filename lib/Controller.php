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

class Controller
{
  protected $module;
  protected $class;
  protected $event;
  private $templateFile;
  protected $title;
  public $presenter;
  public $connected;
  private $initError;
  protected $d_man;
  protected $d_conn;
  protected $cli_mode;
  protected $plugin_manager;
  protected $uri_data;
  protected $stash;
  protected $boot;

  public function __construct($boot)
  {
    $this->boot = $boot;

    $this->cli_mode = $boot->getURICliMode();
    $this->initError = $boot->getURIError();
    $this->uri_data = $boot->getURIData();

    $this->stash = new Stash();

    $this->connected = false;
    $this->module = $_GET['module'];
    $this->class = $_GET['class'];
    $this->event = $_GET['event'];
    $this->title = TITLE; 
    if(TPL_BY_CLASS == 0)
      {
	$this->templateFile = $this->module.'.tpl';
      }
    else
      {
	$this->templateFile = $this->class.'.tpl';
      }
    
    Session::up();
       
    // get presenter
    $this->presenter = $boot->getPresenter();

    if($this->presenter !== null)
      {
	$this->assignSessionVar();
	$this->assignControllerInfo();
	$this->assignConstants();
      }

    if(isset($_GET['section_uri']))
      {
	$this->assign('section', $_GET['section_uri']);
      }
  }


  public function assignConstants()
  {
    $this->assign('NAME', NAME);
    $this->assign('WEB_ROOT', WEB_ROOT);
    $this->assign('PUBLIC_DIR', PUBLIC_DIR);
    $this->assign('DOC_ROOT', DOC_ROOT);
    $this->assign('MVC_VERSION', MVC_VERSION);
    $this->assign('TITLE', TITLE);	
  }


  public function assignControllerInfo()
  {
    $this->assign('module', $this->module);
    $this->assign('class', $this->class);
    $this->assign('event', $this->event);
  }


  public function setTemplate($tpl)
  {
    $this->templateFile = $tpl;
  }
 
  public function initDisplay($i)
  {		
    if(1 || !$this->boot->getPersistentMode())
      // disabling this optimization for now
      {
	$this->presenter->switchInternal($i);       
	$this->presenter->display($this->templateFile);       
      }
  }
  
  public function assignSessionVar()
  {
    foreach($_SESSION as $index => $value)
    {
      $this->assign($index, $value);
    }
  }
   
  public function redirect($endString)
  {    
    if(!$this->boot->getPersistentMode())
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
  

  public function sessionDown()
  {
    Session::down();
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
    Session::set('failed_uri', $uri);
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
	if(isset($_SERVER['HTTP_HOST']))
	  {
	    $this->setFailedURI($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	  }
	Session::set('app_error', array($this->module, $this->class, $message, date('U')));
	//$this->redirect('empathy/error/');       
	throw new Exception('Couldn\'t dispatch: '.$message);
      }
  }

  public function loadUIVars($ui, $ui_array)
  {
    $new_app = Session::getNewApp();
    foreach($ui_array as $setting)
      {
	if(isset($_GET[$setting]))
	  {
	    if(!$new_app)
	      {
		$_SESSION[$ui][$setting] = $_GET[$setting];	    
	      }
	    else
	      {
		Session::setUISetting($ui, $setting, $_GET[$setting]);
	      }
	  }
	elseif(Session::getUISetting($ui, $setting) !== false)
	  {
	    $_GET[$setting] = Session::getUISetting($ui, $setting);
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

  public function assign($name, $data)
  {
    $this->presenter->assign($name, $data);
  }

}
?>