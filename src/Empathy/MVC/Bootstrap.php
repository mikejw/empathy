<?php

declare(strict_types=1);
/**
 * This file is part of the Empathy package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @copyright 2008-2025 Michael J. Whiting
 * @license  See LICENSE
 * @link      https://empathyphp.sh
 */

namespace Empathy\MVC;

use Empathy\MVC\PluginManager\Option as PMOption;

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
    private ?Controller $controller = null;

    /**
     * Default module read from application config file.
     * Used for resolving routes e.g. when URI is empty.
     */
    private ?string $defaultModule = null;

    /**
     * Name of dynamic module, if any.
     * (Usually called 'public_mod').
     * A dynamic module is a module
     * served through the DSection CMS, which
     * is available through ELib.
     */
    private ?string $dynamicModule = null;

    /**
     * Dynamic module (ELib CMS) URI string as fallback (empty when unset).
     */
    private string $dynamicModuleDefaultURI = '';

    /**
     * The URI object is used for determining
     * the correct application controller to dispatch to.
     */
    private ?URI $uri = null;

    /**
     * This property is used to contain a reference to
     * the current instance of the web application.
     */
    private readonly Empathy $mvc;

    /**
     * This property contains a reference to
     * the plugin manager object.
     */
    private PluginManager $pluginManager;

    /**
     * This value of this property is obtained
     * from the (global) application object.
     * When in persistent mode the application
     * is initialized but dispatchment to a
     * controller is prevented. Useful for testing etc.
     */
    private readonly bool $persistentMode;

    /**
     * New property as of 0.9.5.
     * Introduced to prevent
     * low level error messages being returned in
     * an application serving a JSON api.
     */
    private bool $debugMode = false;

    /**
     * New property as of 0.9.5.
     * Application can now run in different environment modes.
     * Currently restricted (enumerated) to 'dev', 'stag', or 'live'.
     */
    private string $environment = 'dev';


    /**
     * Creates the bootstrap object and passes boot options
     * and plugin definition taken from the application config
     * as well as reference to global application object.
     *
     * @param array<string, mixed>       $bootOptions boot options config
     * @param list<array<string, mixed>> $plugins     active plugin definition
     */
    public function __construct(array $bootOptions, /**
     * This property contains a data structure
     * that contains the description of plugins to be initialized.
     * Read from the application config.
     */
    private readonly array $plugins, Empathy $mvc)
    {
        $this->persistentMode = $mvc->getPersistentMode();
        $this->mvc = $mvc;
        $this->pluginManager = DI::getContainer()->get('PluginManager');
        $this->initBootOptions($bootOptions);
    }


    /**
     * Sets local boot options including environment.
     *
     * @param array<string, mixed>|null $bootOptions boot options config
     */
    public function initBootOptions(?array $bootOptions = null): void
    {
        if ($bootOptions === null) {
            $raw = Config::get('BOOT_OPTIONS');
            $bootOptions = is_array($raw) ? $raw : [];
        }
        if (isset($bootOptions['default_module'])) {
            $this->defaultModule = $bootOptions['default_module'];
        }
        if (array_key_exists('dynamic_module', $bootOptions)) {
            $dm = $bootOptions['dynamic_module'];
            $this->dynamicModule = (is_string($dm) && $dm !== '') ? $dm : null;
        }
        if (array_key_exists('dynamic_module_default_uri', $bootOptions)) {
            $uri = $bootOptions['dynamic_module_default_uri'];
            $this->dynamicModuleDefaultURI = is_string($uri) ? $uri : '';
        }

        if (isset($bootOptions['debug_mode'])) {
            $this->debugMode = ($bootOptions['debug_mode'] === true);
        }
        $this->environment = 'dev';
        $validEnv = ['dev', 'uat', 'stag', 'prod'];
        if (isset($bootOptions['environment']) && in_array($bootOptions['environment'], $validEnv, true)) {
            $this->environment = $bootOptions['environment'];
        }
    }


    /**
     * Create URI object which determines dispatch method and
     * perform dispatch.
     *
     * @param boolean $fake Can be used to prevent final action event call.
     * useful for testing.
     * @param string $controller Force controller name. Used in testing.
     */
    public function dispatch($fake = false, $controller = null): void
    {
        $uriService = DI::getContainer()->get('URI');
        if (!$uriService instanceof URI) {
            throw new Exception('DI URI service is missing or invalid');
        }
        $this->uri = $uriService;

        $error = $uriService->getError();

        if ($error === URI::MISSING_CLASS_DEF
           && $this->dynamicModule !== null
           && $this->dynamicModule) {

            // anticipate dispatched errors
            $this->pluginManager = DI::getContainer()->get('PluginManager');
            $this->pluginManager->setOptions([PMOption::DefaultWhitelist]);
            $this->mvc->initPlugins();

            $error = $uriService->dynamicSection();
        }

        if ($error > 0 && $controller === null) {
            if ($this->environment !== 'dev' || $this->debugMode === false) {
                if (in_array($error, [URI::MISSING_CLASS_DEF, URI::MISSING_EVENT_DEF, URI::ERROR_404], true)
                ) {
                    throw new RequestException('Not found', RequestException::NOT_FOUND);
                } elseif ($error === URI::INVALID_DYNAMIC_MODULE_DEFAULT_URI) {
                    throw new RequestException('Bad default dynamic module default uri.', RequestException::BAD_REQUEST);
                }
            } else {
                throw new Exception('Dispatch error '.$error.' : '.$uriService->getErrorMessage());
            }
        }

        if ($controller === null) {
            $controllerName = $uriService->getControllerName();
            $instance = new $controllerName($this);
        } else {
            $instance = new $controller($this);
        }
        if (!$instance instanceof Controller) {
            throw new Exception('Resolved class is not a Controller: '.$instance::class);
        }
        $this->controller = $instance;

        $this->controller->doPreEvent();
        if ($fake === false) {
            $event = $_GET['event'];
            $eventVal = $this->controller->$event();
            if ($this->mvc->hasErrors()) {
                throw new ErrorException($this->mvc->errorsToString());
            } elseif ($eventVal !== false) {
                if ($uriService->getInternal()) {
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
     */
    public function dispatchException(\Throwable $e): void
    {
        $reqError = $e::class === RequestException::class;
        $useSession = $this->controller instanceof \Empathy\MVC\Controller ? $this->controller->getUseSession() : true;

        $this->controller = new Controller($this, $useSession);
        DI::getContainer()->set('Controller', $this->controller);
        $this->controller->doPreEvent();

        $this->controller->viewException($this->debugMode, $e, $reqError);
    }

    /**
     * Invoke the view through the controller.
     * @param boolean $i Whether the current template is internal.
     * E.g. an exception has occurred.
     */
    private function display($i = false): void
    {
        if (!$this->controller instanceof \Empathy\MVC\Controller) {
            throw new Exception('Cannot display: controller was not initialised');
        }
        $this->controller->initDisplay($i);
    }

    /**
     * Cycle through the definition for active plugins
     * and initialize them. Any excepetion that is thrown as
     * a result is cast into an Empathy SafeException.
     * This means error messages will be displayed
     * followed by the application dying silently with no attempt
     * to initialize the view.
     */
    public function initPlugins(): void
    {
        $pluginManager = $this->pluginManager;
        $plugins = $this->plugins;
        $whitelist = $this->pluginManager->getWhitelist();

        if (!$pluginManager->getInitialized()) {
            try {
                $pluginManager->init();

                foreach ($plugins as $p) {
                    if (count($whitelist) && !in_array($p['name'], $whitelist, true)) {
                        continue;
                    }

                    if (isset($p['class_path'])) {
                        $pluginClassName = $p['class_name'] ?? null;
                        $pluginLoader = $p['loader'] ?? null;
                        if (is_string($pluginClassName) && !class_exists($pluginClassName)) {
                            require $p['class_path'];
                            if (is_string($pluginLoader) && $pluginLoader !== '') {
                                try {
                                    $loaderMethod = new \ReflectionMethod($pluginClassName, $pluginLoader);
                                } catch (\ReflectionException) {
                                    $loaderMethod = null;
                                }
                                if ($loaderMethod instanceof \ReflectionMethod && $loaderMethod->isPublic() && $loaderMethod->isStatic()) {
                                    spl_autoload_register(
                                        static function (string $class) use ($loaderMethod): void {
                                            $loaderMethod->invoke(null, $class);
                                        }
                                    );
                                }
                            }
                        }
                    }

                    $plugin = count(explode('\\', (string) $p['name'])) > 1 ? '\\'.$p['name'] : 'Empathy\\MVC\\Plugin\\'.$p['name'];

                    $n = (isset($p['config'])) ?
                        new $plugin($pluginManager, $this, $p['config']) :
                        new $plugin($pluginManager, $this, null);
                    $pluginManager->register($n);
                    $pluginManager->attemptSetView($n);
                    $pluginManager->preDispatch($n);
                }

            } catch (\Exception $e) {
                if (RequestException::class === $e::class) {
                    throw $e;
                } else {
                    throw new \Empathy\MVC\SafeException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }
    }

    /**
     * Returns the current environment.
     * @return string $environment Environment, which is either 'dev', 'stag' or 'live'
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Returns the persistent mode.
     */
    public function getPersistentMode(): bool
    {
        return $this->persistentMode;
    }

    /**
     * Gets value of error property from URI object
     */
    public function getURIError(): mixed
    {
        return $this->uri instanceof \Empathy\MVC\URI ? $this->uri->getError() : null;
    }


    /**
     * Gets value of CLI mode detected during dispatch.
     * by URI object.
     * i.e. the value of $_SERVER['HTTP_HOST'] is null
     * and the value of $_SERVER['REQUEST_URI'] is also null
     */
    public function getURICliMode(): mixed
    {
        return $this->uri instanceof \Empathy\MVC\URI ? $this->uri->getCliMode() : null;
    }

    /**
     * Gets the URI data (data structure representing the current URI).
     *
     * @return array<int, string>|null
     */
    public function getURIData(): ?array
    {
        return $this->uri instanceof \Empathy\MVC\URI ? $this->uri->getData() : null;
    }

    /**
     * Returns value of $debugMode property.
     */
    public function getDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * Get default module.
     */
    public function getDefaultModule(): ?string
    {
        return $this->defaultModule;
    }

    /**
     * Get dynamic module.
     */
    public function getDynamicModule(): ?string
    {
        return $this->dynamicModule;
    }

    /**
     * Get dynamic module default uri.
     */
    public function getDynamicModuleDefaultURI(): string
    {
        return $this->dynamicModuleDefaultURI;
    }

    /**
     * Return mvc object
     */
    public function getMVC(): Empathy
    {
        return $this->mvc;
    }
}
