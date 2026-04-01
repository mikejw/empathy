<?php

declare(strict_types=1);

namespace Empathy\MVC;

use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

define('MVC_VERSION', '4.4.0');


/**
 * Empathy
 * @file            Empathy/Empathy.php
 * @description     Application kernel (dispatch, errors, autoload). YAML is merged into
 *                  {@see Config} and boot data is supplied via {@see BootSnapshot} from
 *                  {@see ConfigBootstrap::apply} before construction.
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
     */
    private ?Bootstrap $boot = null;

    /**
     * Boot options read from application config file.
     *
     * @var array<string, mixed>
     */
    private array $bootOptions = [];

    /**
     * Plugin definition read from application config file.
     *
     * @var list<array<string, mixed>>
     */
    private array $plugins = [];

    /**
     * When application is set to handle errors
     * this array is used to collect the error messages.
     *
     * @var list<string>
     */
    private array $errors = [];

    /**
     * This flag is read from the boot_options section of the application config.
     * If it is true then the main autoload function will attempt to load ELib components
     * when necessary. (There is now no difference in in loading elib components as the
     * common namespace 'vendor'
     * is always the same.)
     */
    private static bool $useElib = false;

    /**
     * Prevent multiple dispatch.
     */
    private bool $dispatchedException = false;


    /**
     * Create application object.
     *
     * @param array<string, mixed>       $bootOptions From {@see ConfigBootstrap::apply}.
     * @param list<array<string, mixed>> $plugins     From {@see ConfigBootstrap::apply}.
     */
    public function __construct(
        private readonly string $configDir,
        private readonly bool $persistentMode,
        array $bootOptions,
        array $plugins,
        private readonly bool $loggingOn = false,
        private readonly ?LoggerInterface $logger = null,
    ) {
        $this->bootOptions = $bootOptions;
        $this->plugins = $plugins;

        spl_autoload_register($this->loadClass(...));

        LogBridge::configure($this->loggingOn, $this->logger);

        if (
            isset($this->bootOptions['use_elib']) &&
            $this->bootOptions['use_elib'] &&
            class_exists(\Empathy\ELib\Config::class)
        ) {
            self::$useElib = true;
            \Empathy\ELib\Config::load($this->configDir);
        } else {
            self::$useElib = false;
        }
        if ($this->getHandlingErrors()) {
            set_error_handler($this->errorHandler(...));
        }
    }


    /**
     * Returns value of handle_errors setting from application config boot options.
     */
    private function getHandlingErrors(): bool
    {
        return (isset($this->bootOptions['handle_errors']) &&
                $this->bootOptions['handle_errors']);
    }

    /**
     * Makes call to plugin initialization of boot object.
     * If application has been configured to handle errors
     * then calls are wrapped in try/catch blocks.
     */
    public function initPlugins(): bool
    {
        $handleSuccess = true;
        $boot = $this->boot ?? throw new \LogicException('Empathy::init() must be called first.');

        if (!$this->getHandlingErrors()) {
            $boot->initPlugins();
        } else {
            try {
                $boot->initPlugins();
            } catch (Exception $e) {
                $this->exceptionHandler($e);
                $handleSuccess = false;
            }
        }
        return $handleSuccess;
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
     *
     */
    public function beginDispatch(bool $fake = false): null | Controller
    {
        $boot = $this->boot ?? throw new \LogicException('Empathy::init() must be called first.');

        if (!$this->getHandlingErrors()) {
            $boot->dispatch($fake);
        } else {
            try {
                $boot->dispatch($fake);
            } catch (Throwable $e) {
                $this->exceptionHandler($e);
            }
        }
        if ($fake) {
            return $boot->getController();
        }
        return null;
    }

    /**
     * The active controller after {@see init()} and dispatch, or null before / between dispatches.
     *
     * Equivalent to {@see Bootstrap::getController()} when the application has been initialised.
     */
    public function getController(): ?Controller
    {
        return $this->boot?->getController();
    }

    /**
     * Returns the $persistentMode setting.
     * @return boolean $persistentMode
     */
    public function getPersistentMode(): bool
    {
        return $this->persistentMode;
    }

    /**
     * Paths for routing and the application document root (from config at call time).
     */
    public function getApplicationPaths(): ApplicationPaths
    {
        return ApplicationPaths::fromConfig($this->configDir);
    }

    /**
     * Returns errors caught by error handler.
     *
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Returns whether error handler has caught anything or not.
     */
    public function hasErrors(): bool
    {
        return (count($this->errors) > 0);
    }


    /**
     * Return a concatenated string of all caught error messages.
     * @return string $errors
     */
    public function errorsToString(): string
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
    public function errorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Only swallow @-suppressed errors: PHP sets error_reporting() to 0 for the duration
        // of the evaluated expression. Bitmask checks against error_reporting() are unreliable
        // inside handlers (e.g. PHPUnit) and break direct calls to this method in tests.
        if (error_reporting() === 0) {
            return true;
        }

        $msg = '';
        switch ($errno) {
            case E_ERROR:
            case E_USER_ERROR:
                $msg = "Error: [$errno] $errstr";
                $msg .= "  Fatal error on line $errline in file $errfile";
                $msg .= ', PHP ' . PHP_VERSION . ' (' . PHP_OS . ')';
                $msg .= ' Aborting...';
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
                // Swallow only this specific PHP 8.5 PDO deprecation (RedBean).
                if (str_contains($errstr, 'MYSQL_ATTR_INIT_COMMAND')) {
                    return true;
                }

                if (($this->boot?->getEnvironment() ?? 'prod') !== 'dev') {
                    return true; // IMPORTANT: swallow in non-dev
                }

                $msg = "Deprecated notice: [$errno] $errstr";
                break;

            default:
                $msg = "Unknown error type: [$errno] $errstr";
                break;
        }

        $msg .= " on line $errline in file $errfile";
        $this->errors[] = $msg;

        return true; // swallow everything handled here
    }

    /**
     * The exception handler.  Deals with any exception.
     *
     *
     *
     */
    public function exceptionHandler(Throwable $e): void
    {
        if ($this->dispatchedException) {
            return;
        }

        $boot = $this->boot ?? throw new \LogicException('Empathy::init() must be called before handling exceptions.');

        $response = '';
        $errors = '';

        if ($this->hasErrors()) {
            $errors = $this->errorsToString();
            $e = new ErrorException($errors);
        }
        if (
            RequestException::class !== $e::class &&
            $boot->getEnvironment() !== 'dev'
        ) {
            $message = '';
            $errors = $e->getMessage();
            if ($boot->getDebugMode()) {
                $message = $e->getMessage();
            }
            // the default is now BAD_REQUEST as we assume the user
            // has done something wrong
            $e = new RequestException($message, RequestException::BAD_REQUEST);
        }

        $response500 = 'HTTP/1.1 500 Internal Server Error';

        switch ($e::class) {
            case SafeException::class:
                Testable::header($response500);
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
                        $response = $response500;
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
                // no break
            default:
                if ($response === '') {
                    $response = $response500;
                }

                $message = $e->getMessage();
                $log = new LogItem(
                    'application error',
                    [],
                    self::class,
                    'error'
                );
                if ($message !== '') {
                    $log->append('exception', $message);
                }
                $log->append('response', $response);
                if ($errors !== '') {
                    $log->append('error', $errors);
                }
                $log->fire();

                Testable::header($response);
                $this->dispatchedException = true;
                $boot->dispatchException($e);
                break;
        }
    }


    /**
     * the autoload function.
     * @param string $classPath the name of class that PHP is attempting to load
     * @throws Exception
     */
    public static function loadClass(string $classPath): void
    {
        $classNameArr = explode('\\', $classPath);
        $className = $classNameArr[ count($classNameArr) - 1 ];

        $location = '';
        if (str_starts_with($classPath, 'Empathy\\MVC\\Controller\\')) {
            if (isset($_GET['module'])) {
                if ($className !== 'CustomController') {
                    $location = Config::get('DOC_ROOT') . '/application/' . $_GET['module'] . '/';
                }
            } else {
                throw new Exception('Module not set.');
            }
        } elseif (str_starts_with($classPath, 'Empathy\\MVC\\Model\\')) {
            $location = Config::get('DOC_ROOT').'/storage/';
        }
        if ($location !== '' && $location !== '0' && file_exists($location.$className.'.php')) {
            $file = $location.$className.'.php';
            include($file);
        }
    }

    public function reloadBootOptions(): void
    {
        ($this->boot ?? throw new \LogicException('Empathy::init() must be called first.'))->initBootOptions();
    }

    public function init(?Bootstrap $bootstrap = null): void
    {
        $this->boot = $bootstrap ?? DI::getContainer()->get('Bootstrap');
        if (!$this->persistentMode) {
            $this->beginDispatch();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getBootOptions(): array
    {
        return $this->bootOptions;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setBootOptions(array $options): void
    {
        $wasHandling = $this->getHandlingErrors();
        $this->bootOptions = $options;
        if ($wasHandling && !$this->getHandlingErrors()) {
            restore_error_handler();
        }
    }

    /**
     * @param list<array<string, mixed>> $plugins
     */
    public function setPlugins(array $plugins): void
    {
        $this->plugins = $plugins;
    }

    public static function usesElib(): bool
    {
        return self::$useElib;
    }
}
