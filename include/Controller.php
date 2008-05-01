<?php

class Controller
{
  protected $module;
  protected $class;
  protected $event;
  protected $templateFile;
  protected $title;
  protected $presenter;
  public $connected;
  private $initError;
  private $internal;
  

  public function __construct($error, $i, $secondary)
  {
    $this->initError = $error;
    $this->internal = $i;
    $this->connected = false;
    $this->module = $_GET['module'];
    $this->class = $_GET['class'];
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

    if(!$secondary)
      {
	$this->sessionUp($GLOBALS['sessionVar']);
         
	if($this->initError == 0 && (!(method_exists($this, $_GET['event']))))
	  {
	    $this->initError = 3;
	  }
	elseif($this->initError == 1 && method_exists($this, $_GET['event']))
	  {
	    $this->initError = 0;
	  }
	
	//    echo $this->initError;
	
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
	    $message = 'Controller event '.$_GET['event'].' has not been defined.';
	    break;
	  default:
	    break;     
	  }
	
	if($this->initError != 0)
	  {
	    $this->error($message);
	  }

    

	/* authentication (admin etc) */
	
	$this->assignSessionVar();
	
	/* assign other data */
	$this->presenter->assign('module', $this->module);
	$this->presenter->assign('class', $this->class);
	$this->presenter->assign('event', $_GET['event']);
	$this->presenter->assign('TITLE', TITLE);	
      }
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
  
  public function sessionUp($sessionVar)
  {    
    session_start();

    for($i = 0; $i < sizeof($sessionVar); $i++)
    {
      session_register($sessionVar[$i]);
    }
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

  public function error($message)
  {
    $this->setFailedURI($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $_SESSION['app_error'] = array($this->module, $this->class, $message, date('U'));
    $this->redirect('empathy/error/');   
  }

    
}
?>