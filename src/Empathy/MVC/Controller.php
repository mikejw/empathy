<?php

declare(strict_types=1);
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

use Empathy\MVC\Plugin\Presentation;

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
    protected ?string $module = null;

    /**
     * The name of the class that the current controller instance belongs to as determined by the URI object.
     * The class will belong to an application module and may well be the same name as the current application module.
     * (A default controller class.)
     */
    protected ?string $class = null;

    /**
     * The name of the current controller action/event. Often this is 'default_event'.
     */
    protected ?string $event = null;

    /**
     * The template file the view will attempt to render.
     */
    private ?string $templateFile = null;

    /**
     * The view/presentation object that will be used to render the page.
     */
    public ?Presentation $presenter = null;

    /**
     * The plugin manager object created during booting.
     */
    protected PluginManager $pluginManager;

    /**
     * Stash object used for storing arbitrary object.
     */
    protected ?Stash $stash = null;

    /**
     * The applications current environment.
     */
    protected ?string $environment = null;

    /**
     * Use session flag
     */
    protected ?bool $useSession = null;

    /**
     * Controller constructor.  Grabs certain properties from the boot object, establishes the view
     * from the plugin manager and assigns certain information to view making it available to templates.
     *
     * @param list<mixed> $pluginOptions
     * @param list<string> $pluginWhitelist
     */
    public function __construct(/**
     * The current bootstrap object.
     */
    protected Bootstrap $boot, bool $useSession = true, array $pluginOptions = [], array $pluginWhitelist = [])
    {
        DI::getContainer()->set('Controller', $this);
        $this->pluginManager = DI::getContainer()->get('PluginManager');
        $this->pluginManager->setController($this);
        $this->pluginManager->setOptions($pluginOptions);
        $this->pluginManager->setWhitelist($pluginWhitelist);

        if ($this->boot->getMVC()->initPlugins()) {
            $this->presenter = $this->pluginManager->getView();
            $this->useSession = $useSession;
            $this->environment = $this->boot->getEnvironment();
            $this->stash = DI::getContainer()->get('Stash');
            $this->module = $_GET['module'] ?? null;
            $this->class = $_GET['class'] ?? null;
            $this->event = $_GET['event'] ?? null;

            $this->templateFile = Config::get('TPL_BY_CLASS') ? $this->class . '.tpl' : $this->module . '.tpl';

            if ($this->useSession) {
                Session::up();
            }
        }
    }

    public function doPreEvent(): void
    {
        $this->pluginManager->preEvent();
        $this->setPresenter($this->pluginManager->getView());
        $this->controllerAssigns();
    }

    private function controllerAssigns(): void
    {
        $this->assignControllerInfo();
        $this->assignConstants();
        $this->assignEnvironment();

        if (isset($_GET['section_uri'])) {
            $this->assign('section', $_GET['section_uri']);
        }

        if ($this->module === 'admin' || $this->boot->getEnvironment() === 'dev') {
            $this->assign('dev_rand', uniqid());
        }
    }

    /**
     * Assigns the value of some of the main settings from the application config to the view.
     */
    private function assignConstants(): void
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
     *
     */
    private function assignControllerInfo(): void
    {
        $this->assign('module', $this->module);
        $this->assign('class', $this->class);
        $this->assign('event', $this->event);
    }


    /**
     * Assign environment value to the view
     *
     *
     */
    private function assignEnvironment(): void
    {
        $this->assign('environment', $this->environment);
    }


    /**
     * Set the name of the current view template
     *
     * @param string $tpl tempalte name (including file extension.)
     */
    public function setTemplate(string $tpl): void
    {
        $this->templateFile = $tpl;
    }

    /**
     * Initialise the view for rendering.
     *
     * @param boolean $internal Whether the template is internal.
     */
    public function initDisplay(bool $internal): void
    {
        if (!$this->presenter instanceof \Empathy\MVC\Plugin\Presentation) {
            throw new Exception('No presentation plugin is available');
        }
        if ($this->templateFile === null) {
            throw new Exception('No template file has been set');
        }
        $this->presenter->display($this->templateFile, $internal);
    }

    /**
     * Redirect the user to another location within the application
     *
     * @param string $endString the new URI to redirect to.
     */
    public function redirect(string $endString = ''): void
    {
        $proto = (\Empathy\MVC\Util\Misc::isSecure()) ? 'https' : 'http';
        if ($this->useSession) {
            Session::write();
        }

        $location = 'Location: ';
        $location .= $proto.'://'.Config::get('WEB_ROOT').Config::get('PUBLIC_DIR').'/';
        if ($endString !== '') {
            $location .= $endString;
        }
        Testable::header($location);
        Testable::doDie('');
    }

    /**
     * Redirect to a local cgi script.
     *
     * @param string $endString path to the script.
     */
    public function redirect_cgi(string $endString = ''): void
    {
        if ($this->useSession) {
            Session::write();
        }
        $location = 'Location: ';
        $location .= 'http://'.Config::get('CGI').'/';
        if ($endString !== '') {
            $location .= $endString;
        }
        Testable::header($location);
    }

    /**
     * End current user session
     */
    public function sessionDown(): void
    {
        if ($this->useSession) {
            Session::down();
        }
    }

    /**
     * Determines whether current request is an ajax request from the browser.
     */
    public function isXMLHttpRequest(): bool
    {
        $request = false;
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
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
     */
    public function assign(string $name, mixed $data, bool $no_array = false): void
    {
        if (!$this->presenter instanceof \Empathy\MVC\Plugin\Presentation) {
            throw new Exception('No presentation plugin is available');
        }
        $this->presenter->assign($name, $data, $no_array);
    }

    /**
     * Retrieve name of current module
     *
     * @return string $module Module name.
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * Retrieve name of current controller class
     *
     * @return string $class Class name.
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * Obtain user interface control values from request/session.
     *
     * @param list<string> $ui_array Set of control settings.
     */
    public function loadUIVars(string $ui, array $ui_array): void
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
     * @param mixed $id The ID.
     * @param mixed $def The default value.
     * @param boolean $assertSet Assert ID is set.
     * @return boolean Init is valid.
     */
    public function initID(mixed $id, mixed $def, bool $assertSet = false): bool
    {
        $valid = true;
        $assign_def = false;

        if (!isset($_GET[$id])) {
            $assign_def = true;
            if ($assertSet) {
                $valid = false;
            }
        } elseif ((string) $_GET[$id] !== (string) (int) $_GET[$id] || ($_GET[$id] === 0 && $def !== 0)
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
     * @param bool $debug Debug mode.
     * @param bool $req_error Is request error. e.g. 404.
     */
    public function viewException(bool $debug, \Throwable $exception, bool $req_error): void
    {
        if (!$this->presenter instanceof \Empathy\MVC\Plugin\Presentation) {
            throw new Exception('No presentation plugin is available');
        }
        $this->presenter->exception($debug, $exception, $req_error);
    }

    /**
     * Assign generated token.
     */
    protected function assignCSRFToken(): void
    {
        $token = md5(uniqid((string) random_int(0, mt_getrandmax()), true));
        $this->assign('csrf_token', $token);
        if ($this->useSession) {
            Session::set('csrf_token', $token);
        }
    }

    public function getUseSession(): bool
    {
        return $this->useSession ?? true;
    }

    private function setPresenter(?Presentation $view): void
    {
        $this->presenter = $view;
    }

}
