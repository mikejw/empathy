<?php

namespace Empathy\MVC;

/**
 * Empathy Controller
 * @package         Empathy
 * @file            Empathy/Controller.php
 * @description     Controller superclass. Application controllers (found within modules) inherit from this class.
 *                  Usually through CustomController.php which resides in the top-level applicaition directory.
 *
 * @author          Mike Whiting
 * @license         LGPLv3
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
     * The name of the class that the current controller instance belongs to as determined by the URI object.
     * The class will belong to an application module and may well be the same name as the current application module.
     * (A default controller class.)
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
        if (Config::get('TPL_BY_CLASS')) {
            $this->templateFile = $this->class.'.tpl';
        } else {
            $this->templateFile = $this->module.'.tpl';
        }
        Session::up();
        $this->presenter = $this->plugin_manager->getView();
        if ($this->presenter !== NULL) {
            $this->assignControllerInfo();
            $this->assignConstants();
            $this->assignEnvironment();
        }
        if (isset($_GET['section_uri'])) {
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
        if (Config::get('NAME') !== false) {
            $this->assign('NAME', Config::get('NAME'));
        }
        if (Config::get('TITLE') !== false) {
            $this->assign('TITLE', Config::get('TITLE'));
        }
        $this->assign('DOC_ROOT', Config::get('DOC_ROOT'));
        $this->assign('WEB_ROOT', Config::get('WEB_ROOT'));
        $this->assign('PUBLIC_DIR', Config::get('PUBLIC_DIR'));
        $this->assign('MVC_VERSION', MVC_VERSION);
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
     * Assign environment value to the view
     *
     * @return void
     *
     */
    private function assignEnvironment()
    {
        $this->assign('environment', $this->environment);
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
     * Redirect the user to another location within the application
     *
     * @param string $endString the new URI to redirect to.
     *
     * @return void
     */
    public function redirect($endString='')
    {
        Session::write();
        $location = 'Location: ';
        $location .= 'http://'.Config::get('WEB_ROOT').Config::get('PUBLIC_DIR').'/';
        if ($endString != '') {
            $location .= $endString;
        }
        Testable::header($location);        
    }

    /**
     * Redirect to a local cgi script.
     *
     * @param string $endString path to the script.
     *
     * @return void
     */
    public function redirect_cgi($endString='')
    {
        Session::write();
        $location = 'Location: ';
        $location .= 'http://'.Config::get('CGI').'/';
        if ($endString != '') {
            $location .= $endString;
        }
        Testable::header($location);
    }

    /**
     * End current user session
     *
     * @return void
     */
    public function sessionDown()
    {
        Session::down();
    }

    /**
     * Determines whether current request is an ajax request from the browser.
     *
     * @return void
     */
    public function isXMLHttpRequest()
    {
        $request = false;
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            $request = true;
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


    // when $def is 0, valid is true when id is 0
    public function initID($id, $def, $assertSet = false)
    {
        $valid = true;
        $assign_def = false;

        if (!isset($_GET[$id])) {
            $assign_def = true;
            if ($assertSet) {
                $valid = false;
            }
        } elseif (!((string) $_GET[$id] === (string) (int) $_GET[$id]) || ($_GET[$id] == 0 && $def != 0)
               || $_GET[$id] < 0) {
            $assign_def = true;
            $valid = false;
        }

        if ($assign_def) {
            $_GET[$id] = $def;
        }

        return $valid;
    }
}
