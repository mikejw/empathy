<?php
/**
 * This file is part of the Empathy package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @copyright 2008-2025 Michael J. Whiting
 * @license  See LICENSE
 * @link      https://www.empathyphp.sh
 */

namespace Empathy\MVC;

/**
 * Main parent controller class.
 *
 * @author Mike Whiting mike@ai-em.net
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
     * The plugin manager object created during booting.
     */
    protected $pluginManager;

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
     * Use session flag
     */
    protected $useSession;

    /**
     * Controller constructor.  Grabs certain properties from the boot object, establishes the view
     * from the plugin manager and assigns certain information to view making it available to templates.
     *
     * @param Bootstrap $boot The current bootstrap object
     */
    public function __construct($boot, $useSession = true, $pluginOptions = [], $pluginWhitelist =[])
    {
        $this->boot = $boot;
        $this->pluginManager = $boot->getPluginManager();
        $this->pluginManager->setOptions($pluginOptions);
        $this->pluginManager->setWhitelist($pluginWhitelist);
        
        if (!$this->boot->getMVC()->initPlugins()) {
            return false;
        }

        $this->pluginManager->setController($this);

        $this->useSession = $useSession;
        $this->environment = $boot->getEnvironment();
        $this->stash =  DI::getContainer()->get('Stash');
        $this->module = (isset($_GET['module']))? $_GET['module']: null;
        $this->class = (isset($_GET['class']))? $_GET['class']: null;
        $this->event = (isset($_GET['event']))? $_GET['event']: null;
        if (Config::get('TPL_BY_CLASS')) {
            $this->templateFile = $this->class.'.tpl';
        } else {
            $this->templateFile = $this->module.'.tpl';
        }
        if ($this->useSession) {
            Session::up();    
        }
        $this->presenter = $this->pluginManager->getView();
        if ($this->presenter !== null) {
            $this->assignControllerInfo();
            $this->assignConstants();
            $this->assignEnvironment();
        }
        if (isset($_GET['section_uri'])) {
            $this->assign('section', $_GET['section_uri']);
        }

        // @todo: create a plugin for this?
        // taken from mikejw custom controller
        if ($boot->getEnvironment() == 'dev') {
            $this->assign('dev_rand', uniqid());
        }
    }

    /**
     * Assigns the value of some of the main settings from the application config to the view.
     *
     * @return null
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
        $this->assign('WEB_ROOT_DEFAULT', Config::get('WEB_ROOT_DEFAULT'));
        $this->assign('SUBDOMAIN', Config::get('SUBDOMAIN'));

        $this->assign('PUBLIC_DIR', Config::get('PUBLIC_DIR'));
        $this->assign('MVC_VERSION', MVC_VERSION);
    }

    /**
     * Assign key controller attributes to the view
     *
     * @return null
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
     * @return null
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
     * @return null
     */
    public function setTemplate($tpl)
    {
        $this->templateFile = $tpl;
    }

    /**
     * Initialise the view for rendering.
     *
     * @param boolean $internal Whether the template is internal.
     * @return null
     */
    public function initDisplay($internal)
    {
        $this->presenter->display($this->templateFile, $internal);
    }


    /**
     * Redirect the user to another location within the application
     *
     * @param string $endString the new URI to redirect to.
     * @return null
     */
    public function redirect($endString = '')
    {
        $proto = (\Empathy\MVC\Util\Misc::isSecure())? 'https': 'http';
        if ($this->useSession) {
            Session::write();    
        }
        
        $location = 'Location: ';
        $location .= $proto.'://'.Config::get('WEB_ROOT').Config::get('PUBLIC_DIR').'/';
        if ($endString != '') {
            $location .= $endString;
        }
        Testable::header($location);
        Testable::doDie('');
    }

    /**
     * Redirect to a local cgi script.
     *
     * @param string $endString path to the script.
     * @return null
     */
    public function redirect_cgi($endString = '')
    {
        if ($this->useSession) {
            Session::write();    
        }  
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
     * @return null
     */
    public function sessionDown()
    {
        if ($this->useSession) {
            Session::down();
        }
    }

    /**
     * Determines whether current request is an ajax request from the browser.
     *
     * @return null
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
     * @param string $name Key name.
     * @param mixed $data Data.
     * @param boolean $no_array Determine if data should be stored 'flat'
     * @return null
     */
    public function assign($name, $data, $no_array = false)
    {
        $this->presenter->assign($name, $data, $no_array);
    }

    /**
     * Retrieve name of current module
     *
     * @return string $module Module name.
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Retrieve name of current controller class
     *
     * @return string $class Class name.
     */
    public function getClass()
    {
        return $this->class;
    }

    public function getEvent() 
    {
        return $this->event;
    }

    /**
     * Obtain user interface control values from request/session.
     * @param string $ui Name of interface control set.
     * @param array $ui_array Set of control settings.
     * @return null
     */
    public function loadUIVars($ui, $ui_array)
    {
        if ($this->useSession) {
            $new_app = Session::getNewApp();
            foreach ($ui_array as $setting) {
                if (isset($_GET[$setting])) {
                    if (!$new_app) {
                        $_SESSION[$ui][$setting] = $_GET[$setting];
                    } else {
                        Session::setUISetting($ui, $setting, $_GET[$setting]);
                    }
                } elseif (Session::getUISetting($ui, $setting) !== false) {
                    $_GET[$setting] = Session::getUISetting($ui, $setting);
                } elseif (isset($_SESSION[$ui][$setting])) {
                    $_GET[$setting] = $_SESSION[$ui][$setting];
                }
            }
        }
    }


    /**
     * When $def is 0, valid is true when id is 0
     * @param int $id The ID.
     * @param mixed $def The default value.
     * @param boolean $assertSet Assert ID is set.
     * @return boolean Init is valid.
     */
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

    /**
     * Send exception to the view.
     * @param boolean $debug Debug mode.
     * @param Exception $exception The exception object.
     * @param boolean $req_error Is request error. e.g. 404.
     * @return null
     */
    public function viewException($debug, $exception, $req_error)
    {
        $this->presenter->exception($debug, $exception, $req_error);
    }

    /**
     * Set the presenter/view object.
     * @param Empathy\MVC\Plugin\Presentation $view The view.
     * @return nulll
     */
    public function setPresenter($view)
    {
        $this->presenter = $view;
    }


    /**
     * Assign generated token.
     * @return null
     */
    protected function assignCSRFToken()
    {
        $token = md5(uniqid(rand(), true));
        $this->assign('csrf_token', $token);
        if ($this->useSession) {
            Session::set('csrf_token', $token);    
        }
    }

    public function getUseSession()
    {
        return $this->useSession;
    }

}
