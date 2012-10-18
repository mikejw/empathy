<?php

namespace Empathy;


/**
 * Empathy Controller
 * @package                     Empathy
 * @file			Empathy/Controller.php
 * @description		        Controller superclass. Application controllers (found within modules) inherit from this class. Usually through CustomController.php which resides in the top-level applicaition directory. 
 * @author			Mike Whiting
 * @license			LGPLv3
 *
 * (c) copyright Mike Whiting 
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class Controller
{

  /** 
   * The module the controller instance belongs to. (Established using the URI object.)
   */
  protected $module;

  /**
   * The name of the class that the current controller instance belongs to as determined by the URI object.  The class will belong to an application module and may well be the same name as the current application module. (A default controller class.)
   */
  protected $class;

 
  /**
   * The name of the current controller action/event. Often this is 'default_event'.
   */
  protected $event;


  /**
   * The template file the view will attempt to render.
   */
  private $templateFile;

  
  /**
   * The default title of the page taken from the application config file.
   */
  protected $title;


  /**
   * The view/presentation object that will be used to render the page.
   */
  public $presenter;


  /**
   * Whether the application has been detected to be running in command line mode.
   */
  protected $cli_mode;


  /**
   * The plugin manager object created during booting.
   */
  protected $plugin_manager;



  /** 
   * Data structure containing the processed URI
   */
  protected $uri_data;


  /**
   * Stash object used for storing arbitrary object.
   */  
  protected $stash;


  /**
   * The current bootstrap object.
   */
  protected $boot;


  /**
   * The applications current environment.
   */
  protected $environment;


  /**
   * Controller constructor.  Grabs certain properties from the boot object, establishes the view
   * from the plugin manager and assigns certain information to view making it available to templates.
   *
   * @param Bootstrap $boot the current bootstrap object
   */   
  public function __construct($boot)
  {
    $this->boot = $boot;

    $this->cli_mode = $boot->getURICliMode();
    $this->initError = $boot->getURIError();
    $this->uri_data = $boot->getURIData();
    $this->plugin_manager = $boot->getPluginManager();
    $this->plugin_manager->setController($this);

    $this->environment = $boot->getEnvironment();

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
    $this->presenter = $this->plugin_manager->getView();


    if($this->presenter !== null)
      {
	$this->assignControllerInfo();
	$this->assignConstants();
      }

    // if within CMS assign the current section name to the template
    if(isset($_GET['section_uri']))
      {
	$this->assign('section', $_GET['section_uri']);
      }
  }


  /**
   * Assigns the value of some of the main settings from the application config to the view.
   *
   * @return void
   */
  private function assignConstants()
  {
    $this->assign('NAME', NAME);
    $this->assign('WEB_ROOT', WEB_ROOT);
    $this->assign('PUBLIC_DIR', PUBLIC_DIR);
    $this->assign('DOC_ROOT', DOC_ROOT);
    $this->assign('MVC_VERSION', MVC_VERSION);
    $this->assign('TITLE', TITLE);	
  }


  /**
   * Assign key controller attributes to the view
   *
   * @return void
   *
   */
  private function assignControllerInfo()
  {
    $this->assign('module', $this->module);
    $this->assign('class', $this->class);
    $this->assign('event', $this->event);
  }


  /**
   * Set the name of the current view template
   *
   * @param string $tpl tempalte name (including file extension.)
   *
   * @return void
   */
  public function setTemplate($tpl)
  {
    $this->templateFile = $tpl;
  }
 
  /** 
   * Initialise the view for rendering
   *
   * @param boolean $i Whether the template is internal.
   *
   * @return void
   */ 
  public function initDisplay($i)
  {		
    $this->presenter->switchInternal($i);       
    $this->presenter->display($this->templateFile);       
  }
  
 
  /**
   * Redirect the user to another location within the application.
   * Redirection is disabled if in command line mode to prevent tests breaking.
   * 
   * @param string $endString the new URI to redirect to.
   * 
   * @return void
   */  
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


  /**
   * Redirect to a local cgi script.
   * 
   * @param string $endString path to the script.
   * 
   * @return void
   */
  protected function redirect_cgi($endString)
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
  

  /** 
   * End current user session
   *
   * @return void
   */
  protected function sessionDown()
  {
    Session::down();
  }


  /** 
   * Determines whether current request is an ajax request from the browser.
   *
   * @return void
   */
  protected function isXMLHttpRequest()
  {
    $request = 0;
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
       ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
      {
	$request = 1;
      }
    return $request;
  }


  /** 
   * Assign value to the current view.
   *
   * @param string $name 
   *
   * @param mixed $data 
   *
   *
   * @return void
   */
  public function assign($name, $data)
  {
    $this->presenter->assign($name, $data);
  }


  /**
   * Retrieve name of current module
   *
   * @return string $module
   */
  public function getModule()
  {
    return $this->module;
  }

  /**
   * Retrieve name of current controller class
   *
   * @return string $class
   */
  public function getClass()
  {
    return $this->class;
  }


  /**
   * Obtain user interface control values from request/session.
   * @param string $ui name of interface control set
   *
   * @param array $ui_array set of control settings
   *
   * @return void
   */
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
  
  
}
?>