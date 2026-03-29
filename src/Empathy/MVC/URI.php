<?php

declare(strict_types=1);

namespace Empathy\MVC;

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
    public const MISSING_CLASS_DEF = 1;

    /**
     * Missing event/action definition
     */
    public const MISSING_EVENT_DEF = 2;

    /**
     * 404 error contant
     */
    public const ERROR_404 = 3;

    /**
     * No template defined constant
     */
    public const NO_TEMPLATE = 4;

    /**
     * Max comparisons contant
     */
    public const MAX_COMP = 4; // maxium relevant info stored in a URI
    // ie module, class, event, id

    /**
     * URI from config error
     */
    public const INVALID_DYNAMIC_MODULE_DEFAULT_URI = 5;

    private ?string $full = null;
    private string $uriString = '';
    /** @var array<int, string> */
    private array $uri = [];
    private ?string $defaultModule = null;
    private int $error = 0;
    private bool $internal = false;
    private string $controllerName = '';
    private bool $cli_mode_detected = false;
    private string $internal_controller = 'empathy';

    public function __construct(?string $default_module, private readonly ?string $dynamicModule, private readonly string $dynamicModuleDefaultURI = '')
    {
        if (isset($_SERVER['HTTP_HOST']) && !str_contains((string) Config::get('WEB_ROOT'), (string) $_SERVER['HTTP_HOST'])) {
            throw new SafeException('Host name mismatch.');
        }

        $this->cli_mode_detected = false;
        $this->sanity($default_module);
        $removeLength = strlen(Config::get('WEB_ROOT').Config::get('PUBLIC_DIR'));
        $this->defaultModule = $default_module;
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->full = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->uriString = substr($this->full, $removeLength + 1);
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            // request has been faked
            $this->uriString = $_SERVER['REQUEST_URI'];
        } else {
            $this->cli_mode_detected = true;
            $this->uriString = '';
        }

        $this->error = 0;
        $this->processRequest();
        $this->setController();
        $this->logRouting();
    }

    /**
     * @return array<int, string>
     */
    public function getData(): array
    {
        return $this->uri;
    }

    public function getCliMode(): bool
    {
        return $this->cli_mode_detected;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function logRouting(): void
    {
        $log = new LogItem(
            'route loaded',
            [
                'module' => $_GET['module'] ?? 'Undefined',
                'class' => $_GET['class'] ?? 'Undefined',
                'event' => $_GET['event'] ?? 'Undefined',
                'controller name' => $this->controllerName,
            ],
            self::class
        );
        $error = $this->getErrorMessage();
        if ($error !== '' && $error !== '0') {
            $log->append('error', $error);
            $log->setMsg('route not loaded');
            $log->setLevel('error');
        }
        $log->fire();
    }

    public function processRequest(): void
    {
        if (isset($_GET['module'])) {
            $this->setModule($_GET['module']);
        } elseif ($this->uriString === '') { // || strpos($this->uriString, '.')) {
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

    public function formURI(): void
    {
        $uri = explode('/', $this->uriString);
        $size = count($uri) - 1;
        // remove empty element caused by trailing slash
        if ($uri[$size] === '') {
            array_pop($uri);
            $size--;
        }

        $args = '';

        // ignore any args
        $lastSegment = $uri[$size] ?? '';
        $queryPos = strpos($lastSegment, '?');
        if ($queryPos !== false) {
            $args = substr($lastSegment, $queryPos);
            $uri[$size] = substr($lastSegment, 0, $queryPos);

            if ($uri[$size] === '') {
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

    public function analyzeURI(): void
    {
        $i = 0;

        $length = count($this->uri);
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
            $this->setModule($this->defaultModule ?? $this->internal_controller);
        }
    }

    private function setModule(string $module): void
    {
        $_GET['module'] = $module;
        if ($_GET['module'] === $this->internal_controller) {
            $this->internal = true;
        }
    }

    private function buildControllerName(string $controller): string
    {
        return 'Empathy\\MVC\\Controller\\'.$controller;
    }

    private function setController(): void
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

        if ($this->error === 0 && !class_exists($this->controllerName)) {
            $this->error = self::MISSING_CLASS_DEF;
        }

        $this->assertEventIsSet();
        if ($this->error === 0) {
            $controllerClass = $this->controllerName;
            if (class_exists($controllerClass)) {
                $r = new \ReflectionClass($controllerClass);
                if (!$r->hasMethod($_GET['event'])) {
                    $this->error = self::MISSING_EVENT_DEF;
                }
            }
        }
    }

    public function assertEventIsSet(): void
    {
        if (!(isset($_GET['event'])) || $_GET['event'] === '') {
            $_GET['event'] = 'default_event';
        }
    }


    public function dynamicSection(): int
    {
        // code still needed to assert correct section path - else throw 404
        $this->error = 0;

        $section = Model::load(SectionItemStandAlone::class);
        $sectionId = -1;

        if ($this->dynamicModule === null || $this->dynamicModule === '') {
            throw new Exception('Failed to find name of dynamic module.');
        } else {
            $_GET['module'] = $this->dynamicModule;
        }

        if ($this->uri === []) {
            $this->uri = explode('/', $this->dynamicModuleDefaultURI);
        }

        $lastIndex = count($this->uri) - 1;
        if (array_key_exists($lastIndex, $this->uri) && is_numeric($this->uri[$lastIndex])) {
            $_GET['id'] = $this->uri[$lastIndex];
            array_pop($this->uri);
        }

        $uriForResolve = array_values($this->uri);

        if ($this->uriString === '' && (bool) $this->dynamicModuleDefaultURI && ($sectionId = $section->resolveURI($uriForResolve)) < 0) {
            $this->error = self::INVALID_DYNAMIC_MODULE_DEFAULT_URI;
        }

        if ($this->error === 0 && (($sectionId = $section->resolveURI($uriForResolve)) < 0)) {
            $this->error = self::ERROR_404;
        }

        if ($sectionId > -1) {
            $_GET['section'] = $sectionId;
            $section->getItem($_GET['section']);
        }

        // section id is not set / found
        if ($section->id < 1) {
            $this->error = self::ERROR_404;
        }

        if ($section->url_name !== null) {
            $_GET['section_uri'] = $section->url_name;
        }

        if ($this->error < 1) {
            if ($section->template === '') {
                $this->error = self::NO_TEMPLATE;
            } elseif ($section->template === '0') {
                // section in 'specialised'
                $controllerName = 'template'.$section->id;
            } else {
                $controllerName = 'template'.$section->template;
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

    public function getErrorMessage(): string
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

    public function sanity(?string $_default_module): void
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

    public function getInternal(): bool
    {
        return $this->internal;
    }
}
