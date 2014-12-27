<?php

namespace Empathy\MVC;

/**
 * Empathy Bootstrap
 * @file			Empathy/Bootstrap.php
 * @description		Bootstrap object for an application using Empathy.
 * @author			Mike Whiting
 * @license			LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class Bootstrap
{

    /**
     * This is used to store a reference to the controller object
     * which is instatiated before an action can be dispatchted.
     * @var Controller
     */
    private $controller = null;

    /**
     * Default module read from application config file.
     * Used for resolving routes e.g. when URI is empty.
     * @var string
     */
    private $defaultModule;

    /**
     * Name of dynamic module, if any.
     * (Usually called 'public_mod').
     * A dynamic module is a module
     * served through the DSection CMS, which
     * is available through ELib.
     * @var string
     */
    private $dynamicModule;

    /**
     * The URI object is used for determining
     * the correct application controller to dispatch to.
     * @var URI
     */
    private $uri;

    /**
     * This property is used to contain a reference to
     * the current instance of the web application.
     * @var Empathy
     */
    private $mvc;

    /**
     * This property contains a data structure
     * that contains the descrition of plugins to be initialized.
     * Read from the application config.
     * @var array
     */
    private $plugins;

    /**
     * This property contains a reference to
     * the plugin manager object.
     * @var PluginManager
     */
    private $plugin_manager;

    /**
     * This value of this property is obtained
     * from the (global) application object.
     * When in persistent mode the application
     * is initialized but dispatchment to a
     * controller is prevented. Useful for testing etc.
     * @var boolean
     */
    private $persistent_mode;

    /**
     * New property as of 0.9.5.
     * Introduced to prevent
     * low level error messages being returned in
     * an application serving a JSON api.
     * @var boolean
     */
    private $debug_mode;

    /**
     * New property as of 0.9.5.
     * Application can now run in different environment modes.
     * Currently restricted (enumerated) to 'dev', 'stag', or 'live'.
     * @var string
     */
    private $environment;

    /**
     * Creates the bootstrap object and passes boot options
     * and plugin definition taken from the application config
     * as well as reference to global application object.
     *
     * @param array $bootOptions boot options config
     *
     * @param array $plugins active plugin defination
     *
     * @param Empathy $mvc the application
     *
     * @return void
     */
    public function __construct($bootOptions, $plugins, $mvc)
    {
        $this->persistent_mode = $mvc->getPersistentMode();

        $this->mvc = $mvc;
        $this->plugins = $plugins;
        $this->plugin_manager = new PluginManager();

        if (isset($bootOptions['default_module'])) {
            $this->defaultModule = $bootOptions['default_module'];
        }
        if (isset($bootOptions['dynamic_module'])) {
            $this->dynamicModule = $bootOptions['dynamic_module'];
        }
        if (isset($bootOptions['debug_mode'])) {
            $this->debug_mode = ($bootOptions['debug_mode'] === true);
        }

        $this->environment = 'dev';
        $valid_env = array('dev', 'stag', 'prod');

        if (isset($bootOptions['environment'])) {
            if (in_array($bootOptions['environment'], $valid_env)) {
                $this->environment = $bootOptions['environment'];
            }
        }
    }

    /**
     * Create URI object which determines dispatch method and
     * perform dispatch
     *
     * @param bool $fake
     *
     * @return void
     */
    public function dispatch($fake=false)
    {
        $this->uri = new URI($this->defaultModule, $this->dynamicModule);
        $error = $this->uri->getError();

        if($error == URI::MISSING_CLASS
           && isset($this->dynamicModule)
           && $this->dynamicModule != '')
        {
            $error = $this->uri->dynamicSection();
        }

        if ($error > 0) {

            if($this->environment == 'prod' || $this->debug_mode == false) {     

                if(
                    $error == URI::MISSING_CLASS ||
                    $error == URI::MISSING_EVENT_DEF ||
                    $error == URI::ERROR_404
                ) {
                        throw new RequestException('Not found', RequestException::NOT_FOUND);
                }
            } else {
                throw new Exception('Dispatch error '.$error.' : '.$this->uri->getErrorMessage());
            }
        }

        $controller_name = $this->uri->getControllerName();
        $this->controller = new $controller_name($this);            
        
        if($fake == false) {
            $event_val = $this->controller->$_GET['event']();
            if ($this->mvc->hasErrors()) {
                throw new ErrorException($this->mvc->errorsToString());
            } elseif ($event_val !== false) {

                if($this->uri->getInternal()) {        
                    $this->controller->setTemplate('empathy.tpl');
                    $this->display(true);
                } else {
                    $this->display(false);                    
                }
            }
        }
    }

    /**
     * If an exception is detected this is used to dispatch
     * to an internal controller and view
     * @param Exception $e the exception object.
     *
     * @return void
     */
    public function dispatchException($e)
    {
        $req_error = (get_class($e) == 'Empathy\MVC\RequestException')? true: false;

        $this->controller = new Controller($this);

        if ($this->controller->getModule() != 'api') {
            $this->controller->assign('error', $e->getMessage());
                        
            if($req_error) {
                 $this->controller->assign('code', $e->getCode());
                 $this->controller->setTemplate('elib:/req_error.tpl');
                 $this->display();
            } else {
                $this->controller->setTemplate('empathy.tpl');
                $this->display(true);
            }           
        } else {
            if (!$this->debug_mode) {
                $r = new \EROb(\ReturnCodes::SERVER_ERROR, 'Server error.');
            } else {
                $r = new \EROb(999, $e->getMessage(), 'SERVER_ERROR_EXPLICIT');
            }

            $this->controller->assign('default', $r);
            $this->display(false);
        }
    }

    /**
     * Invoke the view through the controller
     * @param boolean $i Whether the current template is internal
     * e.g. an exception has occurred.
     *
     * @return void
     */
    private function display($i=false)
    {
        $this->controller->initDisplay($i);
    }

    /**
     * Cycle through the definition for active plugins
     * and initialize them. Any excepetion that is thrown as
     * a result is cast into an Empathy SafeException.
     * This means error messages will be displayed
     * followed by the application dying silently with no attempt
     * to initialize the view.
     *
     * @return void
     */
    public function initPlugins()
    {
        $plugin_manager = $this->plugin_manager;
        $plugins = $this->plugins;

        try {
            if (!$plugin_manager->getInitialized()) {
                $plugin_manager->init();
                foreach ($plugins as $p) {
                    if (isset($p['class_path'])) {
                        require($p['class_path']);
                        if (isset($p['loader']) && $p['loader'] != '') {
                            spl_autoload_register(array($p['class_name'], $p['loader']));
                        }
                    }
                    $plugin_path = realpath(dirname(realpath(__FILE__))).'/Plugin/'.$p['name'].'-'.$p['version'].'.php';
                    if (file_exists($plugin_path)) {
                        require($plugin_path);
                        $plugin = 'Empathy\\MVC\\Plugin\\'.$p['name'];
                        $n = new $plugin();
                        if (isset($p['config'])) {
                            $n->assignConfig($p['config']);
                        }
                        $plugin_manager->register($n);
                    }
                }
                $plugin_manager->preDispatch();
            }
        } catch (\Exception $e) {            
            throw new \Empathy\MVC\SafeException($e->getMessage());
        }
    }

    /**
     * Returns the current environment.
     * @return string $environment Environment, which is either 'dev', 'stag' or 'live'
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Returns the persistent mode.
     * @return boolean $persistentMode
     */
    public function getPersistentMode()
    {
        return $this->persistent_mode;
    }

    /**
     * Gets value of error property from URI object
     * @return integer $error See error class constants in URI class
     */
    public function getURIError()
    {
        return (isset($this->uri))? $this->uri->getError(): null;
    }

    /**
     * Gets value of cli mode detected during
     * by URI object.
     * i.e. the value of $_SERVER['HTTP_HOST'] is null
     * and the value of $_SERVER['REQUEST_URI'] is also null
     *
     * @return boolean $cli_mode Whether the application is running in cli mode.
     */
    public function getURICliMode()
    {
        return (isset($this->uri))? $this->uri->getCliMode(): null;
    }

    /**
     * Gets the URI data (data structure representing the current URI).
     * @return array $uri_data
     */
    public function getURIData()
    {
        return (isset($this->uri))? $this->uri->getData(): null;
    }

    /**
     * Returns plugin manager object.
     * @return PluginManager $plugin_manager
     */
    public function getPluginManager()
    {
        return $this->plugin_manager;
    }

    /**
     * Returns value of $debug_mode property.
     * @return boolean $debug_mode.
     */
    public function getDebugMode()
    {
        return $this->debug_mode;
    }

    /**
     * Returns controller.
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }

}
