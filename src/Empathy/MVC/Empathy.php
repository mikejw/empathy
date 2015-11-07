<?php

namespace Empathy\MVC;


define('MVC_VERSION', '0.9.7');

/**
 * Empathy
 * @file            Empathy/Empathy.php
 * @description     Creates global object that initializes an Empathy application
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
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
     * Create application object.
     * @param string $configDir the location of the application config file
     *
     * @param boolean $persistent_mode Whether the application is running in persistent mode.
     * If true this means there could be many requests following initialization.
     * @return void
     */
    public function __construct($configDir, $persistent_mode = null, $system_mode = false)
    {
        $this->persistent_mode = $persistent_mode;
        if ($system_mode) {
            spl_autoload_register(array($this, 'loadClass'));
        }
        $this->loadConfig($configDir);        
        if ($system_mode) {
            $this->loadConfig(Util\Pear::getConfigDir().'/Empathy');
        } else {
            $this->loadConfig(realpath(dirname(realpath(__FILE__)).'/../../../'));
        }
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
        $this->boot = new Bootstrap($this->bootOptions, $this->plugins, $this);
        $this->initPlugins();
        if ($this->persistent_mode !== true) {
            $this->beginDispatch();
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
        // prioritise any caught errors over exceptions thrown
        if ($this->hasErrors()) {
            $e = new ErrorException($this->errorsToString());
        }

        // checks exception not already of type req
        // then checks env before forcing a req error class
        // (for diplaying standard error pages in prod)
        if ('Empathy\MVC\RequestException' != get_class($e) &&
            $this->boot->getEnvironment() == 'prod') {
            $message = '';
            if ($this->boot->getDebugMode()) {
                $message = $e->getMessage();
            }
            $e = new RequestException($message, RequestException::BAD_REQUEST);
        }

        // force safe exception
        //$e = new Empathy\SafeException($e->getMessage());

        switch (get_class($e)) {
            case 'Empathy\MVC\SafeException':
                Testable::doDie('Safe exception: '.$e->getMessage());
                break;
            case 'Empathy\MVC\TestModeException':
                // allow execution to end naturally
                break;
            case 'Empathy\MVC\RequestException':
                $response = '';
                switch($e->getCode()) {
                    case RequestException::BAD_REQUEST:
                        $response = 'HTTP/1.1 400 Bad Request';
                        $message = 'Bad request';
                        break;
                    case RequestException::NOT_FOUND:
                        $response = 'HTTP/1.0 404 Not Found';
                        break;
                    default:
                        break;
                }
                Testable::header($response);
                //break; do not break! => we want to continue execution to allow exception to be 'dispatched'

            default:
                $this->boot->dispatchException($e);
                break;
        }
    }

    /**
     * read config file from specified location
     * @param  string $configDir
     * @return void
     */
    private function loadConfig($configDir)
    {
        $configFile = $configDir.'/config.yml';
        if (!file_exists($configFile)) {
            die('Config error: '.$configFile.' does not exist');
        }
        $s = new \Spyc();
        $config = $s->YAMLLoad($configFile);
        foreach ($config as $index => $item) {

            // auto fix of doc root
            if (!is_array($item)) {
                if ($index == 'doc_root') {
                    if (!file_exists($item)) {
                        $item = $configDir;
                    }
                }
            }
            Config::store(strtoupper($index), $item);
        }
        if (isset($config['boot_options'])) {
            $this->bootOptions = $config['boot_options'];
        }
        if (isset($config['plugins'])) {
            $this->plugins = $config['plugins'];
        }
    }

    /**
     * the autoload function.
     * @param  string $class the name of class that PHP is attempting to load
     * @return void
     */
    public static function loadClass($class)
    {       
        $i = 0;
        $load_error = 1;
        $location = array('');
        if (strpos($class, 'Controller\\')
           || strpos($class, 'Model\\')) {
            $class_arr = explode('\\', $class);
            $class = $class_arr[sizeof($class_arr)-1];

            if (isset($_GET['module'])) {
                array_push($location, DOC_ROOT.'/application/'.$_GET['module'].'/');
            }
            array_push($location, DOC_ROOT.'/storage/');
        } elseif (strpos($class, 'Empathy') === 0) {
            $class = str_replace('\\', '/', $class);
        }
        array_push($location, DOC_ROOT.'/application/');

        while ($i < sizeof($location) && $load_error == 1) {
            $class_file = $location[$i].$class.'.php';

            //echo $class_file.'<br />';

            if (@include($class_file)) {
                $class_file.": 1<br />\n";
                $load_error = 0;
            } else {
                $class_file.": 0<br />\n";
            }
            $i++;
        }
    }



    public function reloadBootOptions()
    {
        $this->boot->initBootOptions();
    }
}
