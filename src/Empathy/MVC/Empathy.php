<?php

namespace Empathy\MVC;

define('MVC_VERSION', '0.10.0');


/**
 * Empathy
 * @file            Empathy/Empathy.php
 * @description     Creates global object that initializes an Empathy application
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
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
    private $bootOptions = array();

    /**
     * Plugin definition read from application config file.
     * @var array
     */
    private $plugins = array();

    /**
     * When application is set to handle errors
     * this array is used to collect the error messages.
     * @var array
     */
    private $errors = array();

    /**
     * Application persistent mode. Implies there could be multiple requests to handle
     * following initialization. This flag is passed directly to the application.
     * @var boolean
     */
    private $persistent_mode = false;

    /**
     * This flag is read from the boot_options section of the application config.
     * If it is true then the main autoload function will attempt to load ELib components
     * when necessary. (There is now no difference in in loading elib components as the
     * common namespace 'vendor'
     * is always the same.)
     *
     * @var boolean
     */
    private static $use_elib = false;

    /**
     * Set config into memory.
     * 
     * @param $config Configuration data 
     * 
     * @param $hard   Set constants  
     * 
     * @return void
     */
    private function consumeConfig($config, $configDir, $hard = false)
    {
        foreach ($config as $index => &$item) {
            // auto fix of doc root
            if (!is_array($item)) {
                if ($index === 'doc_root') {
                    if (!file_exists($item)) {
                        $item = $configDir;
                    }
                }
            }

            if ($hard) {
                if (!defined(strtoupper($index))) {
                    define(strtoupper($index), $item);
                }
            } else {
                Config::store(strtoupper($index), $item);
            }
        }
        if (isset($config['boot_options'])) {
            $this->bootOptions = $config['boot_options'];
        }
        if (isset($config['plugins'])) {
            $this->plugins = $config['plugins'];
        }
    }

    /**
     * Create application object.
     * @param string $configDir the location of the application config file
     *
     * @param boolean $persistent_mode Whether the application is running in persistent mode.
     * If true this means there could be many requests following initialization.
     * @return void
     */
    public function __construct($configDir, $persistent_mode = false)
    {
        $this->persistent_mode = $persistent_mode;
        spl_autoload_register(array($this, 'loadClass'));

        list($appConfig, $globalConfig) = DI::getContainer()->get('Config');
        $this->consumeConfig($appConfig, $configDir);
        $this->consumeConfig($globalConfig, $configDir, true);        

        if (isset($this->bootOptions['use_elib']) &&
           $this->bootOptions['use_elib']) {
            self::$use_elib = true;
            \Empathy\ELib\Config::load($configDir);
        } else {
            self::$use_elib = false;
        }
        if ($this->getHandlingErrors()) {
            set_error_handler(array($this, 'errorHandler'));
        }
    }


    /**
     * Returns value of handle_errors setting from application config boot options.
     * @return void
     */
    private function getHandlingErrors()
    {
        return (isset($this->bootOptions['handle_errors']) &&
                $this->bootOptions['handle_errors']);
    }

    /**
     * Makes call to plugin initialization of boot object.
     * If application has been configured to handle errors
     * then calls are wrapped in try/catch blocks.
     *
     * @return void
     */
    public function initPlugins()
    {
        if (!$this->getHandlingErrors()) {
            $this->boot->initPlugins();
        } else {
            try {
                $this->boot->initPlugins();
            } catch (\Exception $e) {                
                $this->exceptionHandler($e);
            }
        }
    }

    /**
     * Dispatch to controller via boot object.
     * If application has been configured to handle errors
     * then calls are wrapped in try/catch blocks.
     *
     * @param bool $fake value is passed to the dispatch method of boot
     * which will (if true) not call the controller action. the
     * controller object is then returned.
     *
     * @return void/Controller
     *
     */
    public function beginDispatch($fake = false)
    {
        if (!$this->getHandlingErrors()) {
            $this->boot->dispatch($fake);
        } else {
            try {
                $this->boot->dispatch($fake);
            } catch (\Exception $e) {
                $this->exceptionHandler($e);
            }
        }
        if ($fake) {
            return $this->boot->getController();
        }
    }

    /**
     * Returns the $persistent_mode setting.
     * @return boolean $persistent_mode
     */
    public function getPersistentMode()
    {
        return $this->persistent_mode;
    }

    /**
     * Returns errors caught by error handler.
     * @return array $errors
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * Returns whether error handler has caught anything or not.
     * @return boolean
     */
    public function hasErrors()
    {
        return (sizeof($this->errors) > 0);
    }


    /**
     * Return a concatenated string of all caught error messages.
     * @return string $errors
     */
    public function errorsToString()
    {
        return implode('</h2><h2>&nbsp;</h2><h2>', $this->getErrors());
    }

    /**
     * The error handling function.
     *
     * @param integer $errno the type of error.
     *
     * @param string $errstr the error message which has been produced
     *
     * @param string $errfile the name of the file which caused the error
     *
     * @param integer $errline the line number of the error
     *
     * @return boolean returns true to indicate the error has been handled.
     *
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting()) {
            $msg = '';
            switch ($errno) {
                case E_ERROR:
                case E_USER_ERROR:
                    $msg = "Error: [$errno] $errstr";
                    $msg .= "  Fatal error on line $errline in file $errfile";
                    $msg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")";
                    $msg .= " Aborting...";
                    Testable::doDie($msg);
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

    /**
     * The exception handler.  Deals with any exception.
     *
     * @param Exception $e
     *
     * @return void
     *
     */
    public function exceptionHandler($e)
    {
        $response = '';
        if ($this->hasErrors()) {
            $e = new ErrorException($this->errorsToString());
        }

        if (
            RequestException::class != get_class($e) &&
            $this->boot->getEnvironment() != 'dev'
        ) {
            $message = '';
            if ($this->boot->getDebugMode()) {
                $message = $e->getMessage();
            }
            // the default is now BAD_REQUEST as we assume the user
            // has done something wrong
            $e = new RequestException($message, RequestException::BAD_REQUEST);
        }

        switch (get_class($e)) {
            case SafeException::class:
                Testable::doDie('Safe exception: '.$e->getMessage());
                break;
            case TestModeException::class:
                // allow execution to end naturally
                break;
            case RequestException::class:
                switch ($e->getCode()) {
                    case RequestException::BAD_REQUEST:
                        $response = 'HTTP/1.1 400 Bad Request';
                        break;
                    case RequestException::NOT_FOUND:
                        $response = 'HTTP/1.1 404 Not Found';
                        break;
                    case RequestException::INTERNAL_ERROR:
                        $response = 'HTTP/1.1 500 Internal Server Error';
                        break;
                    case RequestException::NOT_AUTHORIZED:
                        $response = 'HTTP/1.1 403 Forbidden';
                        break;   
                    case RequestException::NOT_AUTHENTICATED:
                        $response = 'HTTP/1.1 401 Unauthorized';
                        break;
                    case RequestException::METHOD_NOT_ALLOWED:
                        $response = 'HTTP/1.1 401 Method Not Allowed';
                        break;                              
                    default:
                        break;
                }
            default:
                if ($response == '') {
                    $response = 'HTTP/1.1 500 Internal Server Error';
                }
                Testable::header($response);
                $this->boot->dispatchException($e);
                break;
        }
    }


    /**
     * the autoload function.
     * @param  string $class the name of class that PHP is attempting to load
     * @return void
     */
    public static function loadClass($classPath)
    {
        $classNameArr = explode('\\', $classPath);
        $className = $classNameArr[ sizeof($classNameArr) -1 ];

        $location = '';
        if (strpos($classPath, 'Empathy\\MVC\\Controller\\') === 0) {
            if (isset($_GET['module'])) {
                if ($className != 'CustomController') {
                    $location = Config::get('DOC_ROOT') . '/application/' . $_GET['module'] . '/';
                }
            } else {
                throw new \Exception('Module not set.');
            }
        } elseif (strpos($classPath, 'Empathy\\MVC\\Model\\') === 0) {
            $location = Config::get('DOC_ROOT').'/storage/';
        }
        if (!empty($location) && file_exists($location.$className.'.php')) {
            $file = $location.$className.'.php';
            include($file);
        }
    }


    public function reloadBootOptions()
    {
        $this->boot->initBootOptions();
    }



    // DI
    public function init()
    {

        $this->boot = DI::getContainer()->get('Bootstrap');
        $this->initPlugins();
        if ($this->persistent_mode !== true) {
            $this->beginDispatch();
        }
    }

    public function getBootOptions()
    {
        return $this->bootOptions;
    }
    public function getPlugins()
    {
        return $this->plugins;
    }

    public function setBootOptions($options)
    {
        $this->bootOptions = $options;
    }
    public function setPlugins($plugins)
    {
        $this->plugins = $plugins;
    }
}
