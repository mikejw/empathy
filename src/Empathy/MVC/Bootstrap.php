<?php
/**
 * This file is part of the Empathy package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @copyright 2008-2016 Mike Whiting
 * @license  See LICENSE
 * @link      http://www.empathyphp.co.uk
 */

namespace Empathy\MVC;


/**
 * Main boot class that handles plugins and dispatches to controllers.
 *
 * @author Mike Whiting mike@ai-em.net
 */
class Bootstrap
{

    /**
     * This is used to store a reference to the controller object
     * which is instatiated before an action can be dispatchted.
     */
    private $controller = null;

    /**
     * Default module read from application config file.
     * Used for resolving routes e.g. when URI is empty.
     */
    private $defaultModule;

    /**
     * Name of dynamic module, if any.
     * (Usually called 'public_mod').
     * A dynamic module is a module
     * served through the DSection CMS, which
     * is available through ELib.
     */
    private $dynamicModule;

    /**
     * @var Dynamic module (ELib CMS) URI string as fallback
     */
    private $dynamicModuleDefaultURI;

    /**
     * The URI object is used for determining
     * the correct application controller to dispatch to.
     */
    private $uri;

    /**
     * This property is used to contain a reference to
     * the current instance of the web application.
     */
    private $mvc;

    /**
     * This property contains a data structure
     * that contains the description of plugins to be initialized.
     * Read from the application config.
     */
    private $plugins;

    /**
     * This property contains a reference to
     * the plugin manager object.
     */
    private $plugin_manager;

    /**
     * This value of this property is obtained
     * from the (global) application object.
     * When in persistent mode the application
     * is initialized but dispatchment to a
     * controller is prevented. Useful for testing etc.
     */
    private $persistent_mode;

    /**
     * New property as of 0.9.5.
     * Introduced to prevent
     * low level error messages being returned in
     * an application serving a JSON api.
     */
    private $debug_mode;

    /**
     * New property as of 0.9.5.
     * Application can now run in different environment modes.
     * Currently restricted (enumerated) to 'dev', 'stag', or 'live'.
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
        $this->plugin_manager = DI::getContainer()->get('PluginManager');
        $this->initBootOptions($bootOptions);
    }


    /**
     * Sets local boot options including environment.
     *
     * @param array $bootOptions boot options config
     * @return null
     */
    public function initBootOptions($bootOptions = null)
    {
        if ($bootOptions === null) {
            $bootOptions = Config::get('BOOT_OPTIONS');
        }
        if (isset($bootOptions['default_module'])) {
            $this->defaultModule = $bootOptions['default_module'];
        }
        if (isset($bootOptions['dynamic_module'])) {
            $this->dynamicModule = $bootOptions['dynamic_module'];
        }
        if (isset($bootOptions['dynamic_module_default_uri'])) {
            $this->dynamicModuleDefaultURI = $bootOptions['dynamic_module_default_uri'];
        }

        if (isset($bootOptions['debug_mode'])) {
            $this->debug_mode = ($bootOptions['debug_mode'] === true);
        }
        $this->environment = 'dev';
        $valid_env = array('dev', 'uat', 'stag', 'prod');
        if (isset($bootOptions['environment'])) {
            if (in_array($bootOptions['environment'], $valid_env)) {
                $this->environment = $bootOptions['environment'];
            }
        }
    }


    /**
     * Create URI object which determines dispatch method and
     * perform dispatch.
     *
     * @param boolean $fake Can be used to prevent final action event call.
     * useful for testing.
     * @param string $controller Force controller name. Used in testing.
     * @return null
     */
    public function dispatch($fake = false, $controller = null)
    {
        $this->uri = DI::getContainer()->get('URI');

        $error = $this->uri->getError();

        if ($error == URI::MISSING_CLASS_DEF
           && isset($this->dynamicModule)
           && $this->dynamicModule != '') {
            $error = $this->uri->dynamicSection();
        }

        if ($error > 0 && $controller === null) {
            if ($this->environment != 'dev' || $this->debug_mode == false) {
                if ($error == URI::MISSING_CLASS_DEF ||
                    $error == URI::MISSING_EVENT_DEF ||
                    $error == URI::ERROR_404
                ) {
                    throw new RequestException('Not found', RequestException::NOT_FOUND);
                }
            } else {
                throw new Exception('Dispatch error '.$error.' : '.$this->uri->getErrorMessage());
            }
        }

        if ($controller === null) {
            $controller_name = $this->uri->getControllerName();
            $this->controller = new $controller_name($this);
        } else {
            $this->controller = new $controller($this);
        }

        
        $this->plugin_manager->preEvent();

        if ($fake == false) {
            $event = $_GET['event'];
            $event_val = $this->controller->$event();
            if ($this->mvc->hasErrors()) {
                throw new ErrorException($this->mvc->errorsToString());
            } elseif ($event_val !== false) {
                if ($this->uri->getInternal()) {
                    $this->controller->assign('centerpage', true);
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
     * to an internal controller and view.
     * @param Exception $e The exception object.
     * @return null
     */
    public function dispatchException($e)
    {
        $req_error = (get_class($e) == RequestException::class) ? true : false;
        $useSession = $this->controller !== null ? $this->controller->getUseSession() : true;
        $this->controller = new Controller($this, $useSession); 
        $this->plugin_manager->preEvent();
        $this->controller->viewException($this->debug_mode, $e, $req_error);
    }

    /**
     * Invoke the view through the controller.
     * @param boolean $i Whether the current template is internal.
     * E.g. an exception has occurred.
     * @return null
     */
    private function display($i = false)
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
     * @return null
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
                        if (!class_exists($p['class_name'])) {
                            require($p['class_path']);
                            if (isset($p['loader']) && $p['loader'] != '') {
                                spl_autoload_register(array($p['class_name'], $p['loader']));
                            }
                        }
                    }

                    if (count(explode('\\', $p['name'])) > 1) {
                        $plugin = '\\'.$p['name'];
                    } else {
                        $plugin = 'Empathy\\MVC\\Plugin\\'.$p['name'];
                    }

                    $n = (isset($p['config']))?
                        new $plugin($plugin_manager, $this, $p['config']):
                        new $plugin($plugin_manager, $this, null);
                    $plugin_manager->register($n);
                }
                
                \Empathy\MVC\DI::getContainer()->set($p['name'], $n);
                
                $plugin_manager->preDispatch();
            }
        } catch (\Exception $e) {
            if (RequestException::class === get_class($e)) {
                throw $e;
            } else {
                throw new \Empathy\MVC\SafeException($e->getMessage());
            }
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
     * @return integer $error See error class constants in URI class.
     */
    public function getURIError()
    {
        return (isset($this->uri))? $this->uri->getError(): null;
    }


    /**
     * Gets value of CLI mode detected during dispatch.
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

    /**
     * Get default module.
     *  @return string Module.
     */
    public function getDefaultModule()
    {
        return $this->defaultModule;
    }

    /**
     * Get dynamic module.
     * @return string Dynamic module.
     */
    public function getDynamicModule()
    {
        return $this->dynamicModule;
    }

    /**
     * Get dynamic module default uri.
     * @return string Dynamic module default uri.
     */
    public function getDynamicModuleDefaultURI()
    {
        return $this->dynamicModuleDefaultURI;
    }

    /**
     * Return mvc object
     */
    public function getMVC()
    {
        return $this->mvc;
    }
}
