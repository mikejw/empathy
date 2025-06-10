<?php

namespace Empathy\MVC;

use Empathy\MVC\LogItem;
use Empathy\MVC\SectionItemStandAlone;

/**
 * Empathy URI
 * @file            Empathy/URI.php
 * @description     Analyize URI and determine route to appliction module, controller class and event/action.
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class URI
{
    /**
     * Missing class definition constant
     */
    const MISSING_CLASS_DEF = 1;

    /**
     * Missing event/action definition
     */
    const MISSING_EVENT_DEF = 2;

    /**
     * 404 error contant
     */
    const ERROR_404 = 3;

    /**
     * No template defined constant
     */
    const NO_TEMPLATE = 4;

    /**
     * Max comparisons contant
     */
    const MAX_COMP = 4; // maxium relevant info stored in a URI
    // ie module, class, event, id

    /**
     * URI from config error
     */
    const INVALID_DYNAMIC_MODULE_DEFAULT_URI = 5;

    private $full;
    private $uriString;
    private $uri;
    private $defaultModule;
    private $dynamicModule;
    private $dynamicModuleDefaultURI;
    private $error = 0;
    private $internal = false;
    private $controllerName = '';
    private $cli_mode_detected;
    private $internal_controller = 'empathy';

    public function __construct($default_module, $dynamic_module, $dynamic_module_default_uri = '')
    {
        if (isset($_SERVER['HTTP_HOST']) && strpos(Config::get('WEB_ROOT'), $_SERVER['HTTP_HOST']) === false) {
            throw new SafeException('Host name mismatch.');
        }

        $this->cli_mode_detected = false;
        $this->sanity($default_module);
        $removeLength = strlen(Config::get('WEB_ROOT').Config::get('PUBLIC_DIR'));
        $this->defaultModule = $default_module;
        $this->dynamicModule = $dynamic_module;
        $this->dynamicModuleDefaultURI = $dynamic_module_default_uri;
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->full = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->uriString = substr($this->full, $removeLength + 1);
        } else {
            if (isset($_SERVER['REQUEST_URI'])) { // request has been faked
                $this->uriString = $_SERVER['REQUEST_URI'];
            } else {
                $this->cli_mode_detected = true;
            }
        }

        $this->error = 0;
        $this->processRequest();
        $this->setController();
        $this->logRouting();
    }

    public function getData()
    {
        return $this->uri;
    }

    public function getCliMode()
    {
        return $this->cli_mode_detected;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getControllerName()
    {
        return $this->controllerName;
    }

    public function logRouting()
    {
       $log = new LogItem(
           'route loaded',
           array(
               'module' => $_GET['module'] ?? 'Undefined',
               'class' => $_GET['class'] ?? 'Undefined',
               'event' => $_GET['event'] ?? 'Undefined',
               'controller name' => $this->controllerName,
           ),
           self::class
       );
        if ($error = $this->getErrorMessage()) {
            $log->append('error', $error);
            $log->setMsg('route not loaded');
            $log->setLevel('error');
        }
        $log->fire();
    }

    public function processRequest()
    {
        if (isset($_GET['module'])) {
            $this->setModule($_GET['module']);
        } elseif ($this->uriString == '') { // || strpos($this->uriString, '.')) {
            if ($this->defaultModule === null) {
                $this->setModule($this->internal_controller);
            } else {
                $this->setModule($this->defaultModule);
            }
        } else {
            $this->formURI();
            $this->analyzeURI();
        }
    }

    public function formURI()
    {
        $uri = explode('/', $this->uriString);
        $size = sizeof($uri) - 1;
        // remove empty element caused by trailing slash
        if ($uri[$size] == '') {
            array_pop($uri);
            $size--;
        }

        $args = '';

        // ignore any args
        if (preg_match('/\?/', $uri[$size])) {
            $start_args = strpos($uri[$size], '?');
            $args = substr($uri[$size], $start_args);
            $uri[$size] = substr($uri[$size], 0, $start_args);

            if ($uri[$size] == '') {
                array_pop($uri);
            }
        }

        // check for uppercase letters in main uri
        // and redirect if present
        
        $temp_uri_string = implode('/', $uri);
        if (preg_match('/[A-Z]/', $temp_uri_string)) {
            header(
                'Location: http://'.Config::get('WEB_ROOT')
                    .Config::get('PUBLIC_DIR')
                    .'/'.strtolower($temp_uri_string).$args,
                true,
                301
            );
            exit();
        }
        $this->uri = $uri;
    }

    public function analyzeURI()
    {
        $i = 0;

        $length = sizeof($this->uri);
        if ($length > self::MAX_COMP) {
            $length = self::MAX_COMP;
        }

        while ($i < $length) {
            $current = $this->uri[$i];

            if (!isset($_GET['id']) && is_numeric($current)) {
                $_GET['id'] = $current;
                $i++;
                continue;
            }

            if (!isset($_GET['module'])) {
                $this->setModule($current);
                $i++;
                continue;
            }

            if (!isset($_GET['class'])) {
                $_GET['class'] = $current;
                $i++;
                continue;
            }

            if (!isset($_GET['event'])) {
                $_GET['event'] = $current;
            }
            $i++;
        }
        if (!isset($_GET['module'])) {
            // only present url param is an id
            $this->setModule($this->defaultModule);
        }
    }

    private function setModule($module)
    {
        $_GET['module'] = $module;
        if ($_GET['module'] == $this->internal_controller) {
            $this->internal = true;
        }
    }

    private function buildControllerName($controller)
    {
        return 'Empathy\\MVC\\Controller\\'.$controller;
    }

    private function setController()
    {
        require_once(Config::get('DOC_ROOT').'/application/CustomController.php');

        if (!(isset($_GET['class'])) && isset($_GET['module'])) {
            $_GET['class'] = $_GET['module'];
        }

        if (isset($_GET['class'])) {
            $this->controllerName = $_GET['class'];
        }

        if (!class_exists($this->buildControllerName($this->controllerName))) {
            if (isset($_GET['class'])) {
                $_GET['event'] = $_GET['class'];
            }
            $_GET['class'] = $_GET['module'];
            $this->controllerName = $_GET['module'];
        }
    
        $this->controllerName = $this->buildControllerName($this->controllerName);

        if (!$this->error) {
            if (!class_exists($this->controllerName)) {
                $this->error = self::MISSING_CLASS_DEF;
            }
        }

        $this->assertEventIsSet();
        if (!$this->error) {
            $r = new \ReflectionClass($this->controllerName);
            if (!$r->hasMethod($_GET['event'])) {
                $this->error = self::MISSING_EVENT_DEF;
            }
        }
    }

    public function assertEventIsSet()
    {
        if (!(isset($_GET['event'])) || $_GET['event'] == '') {
            $_GET['event'] = 'default_event';
        }
    }


    public function dynamicSection()
    {
        // code still needed to assert correct section path - else throw 404
        $this->error = 0;

        $section = Model::load(SectionItemStandAlone::class);
        $sectionId = -1;

        if (!isset($this->dynamicModule) || $this->dynamicModule == '') {
            throw new Exception("Failed to find name of dynamic module.");
        } else {
            $_GET['module'] = $this->dynamicModule;
        }

        if (!isset($this->uri)) {
            $this->uri = explode('/', $this->dynamicModuleDefaultURI);
        }

        if (isset($this->uri) && sizeof($this->uri) > 0) {
            $section_index = (sizeof($this->uri) - 1);
            if (is_numeric($this->uri[$section_index])) {
                $_GET['id'] = $this->uri[$section_index];
                array_pop($this->uri);
            }
        }

        if ($this->uriString === '' && !!$this->dynamicModuleDefaultURI) {
            if (($sectionId = $section->resolveURI($this->uri)) < 0) {
                $this->error = self::INVALID_DYNAMIC_MODULE_DEFAULT_URI;
            }
        }

        if ($this->error === 0 && (($sectionId = $section->resolveURI($this->uri)) < 0)) {
            $this->error = self::ERROR_404;
        }

        if ($sectionId > -1) {
            $_GET['section'] = $sectionId;
            $section->getItem($_GET['section']);
        }

        // section id is not set / found
        if (!(is_numeric($section->id))) {
            $this->error = self::ERROR_404;
        }

        if (isset($section->url_name)) {
            $_GET['section_uri'] = $section->url_name;
        }

        if ($this->error < 1) {
            if ($section->template == "") {
                $this->error = self::NO_TEMPLATE;
            } else {
                if ($section->template == '0') { // section in 'specialised'
                    $controllerName = "template".$section->id;
                } else {
                    $controllerName = "template".$section->template;
                }
            }
        }

        if (isset($controllerName)) {
            $_GET['class'] = $controllerName;
        }

        $_GET['event'] = 'default_event';
        
        if ($this->error < 1) {
            $this->setController();
        }

        return $this->error;
    }

    public function getErrorMessage()
    {
        $message = '';
        switch ($this->error) {
            case self::MISSING_CLASS_DEF:
                $message = 'Missing or incorrect class definition';
                break;
            case self::MISSING_EVENT_DEF:
                $message = 'Controller event '.$_GET['event'].' has not been defined';
                break;
            case self::ERROR_404:
                $message = 'Error 404';
                break;
            case self::NO_TEMPLATE:
                $message = 'No DSection template specified';
                break;
            case self::INVALID_DYNAMIC_MODULE_DEFAULT_URI:
                $message = 'Dynamic module default URI is invalid';
                break;
            default:
                break;
        }
        return $message;
    }

    public function sanity($default_module)
    {
        if (Config::get('WEB_ROOT') === false) {
            throw new SafeException('Dispatch error: Web root is not defined');
        }
        if (Config::get('PUBLIC_DIR') === false) {
            throw new SafeException('Dispatch error: Public dir is not defined');
        }
        if (Config::get('DOC_ROOT') === false) {
            throw new SafeException('Dispatch error: Doc root is not defined');
        }
    }

    public function getInternal()
    {
        return $this->internal;
    }
}
