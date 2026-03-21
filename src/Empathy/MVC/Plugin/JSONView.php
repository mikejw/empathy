<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\Config;
use Empathy\MVC\DI;
use Empathy\MVC\PluginManager;
use Empathy\MVC\RequestException;
use Empathy\MVC\Testable;

/**
 * Empathy JSONView Plugin
 * @file            Empathy/MVC/Plugin/JSONView.php
 * @description
 * @author          Michael J. Whiting
 * @license         See LICENCE
 *
 * (c) copyright Michael J. Whiting

 * with this source code in the file LICENSE
 */
class JSONView extends PresentationPlugin implements PreEvent, Presentation
{
    /** @var array<string, mixed>|object|null */
    private mixed $output = null;

    /** @var class-string */
    private string $error_ob = JSONView\EROb::class;

    /** @var class-string */
    private string $return_ob = JSONView\ROb::class;

    /** @var class-string */
    private string $return_codes = JSONView\ReturnCodes::class;

    private bool $prettyPrint = false;

    private bool $errorResponse = false;

    public function __construct(PluginManager $manager, Bootstrap $bootstrap, mixed $config)
    {
        parent::__construct($manager, $bootstrap, $config, false);
    }

    public function assign(string $name, mixed $data, bool $no_array = false): void
    {
        if ($no_array) {
            $this->output = $data;
        } else {
            if ($this->output !== null && is_object($this->output)) {
                $this->clearVars();
            }
            if (!is_array($this->output)) {
                $this->output = [];
            }
            $this->output[$name] = $data;
        }
    }


    private function isResponseSubClass(object $object): bool
    {
        $isError = $object instanceof $this->error_ob
            || $object instanceof JSONView\EROb;
        $isReturn = $object instanceof $this->return_ob
            || $object instanceof JSONView\ROb;
        $this->errorResponse = $isError;

        return $isError || $isReturn;
    }

    public function display(string $template, bool $internal = false): void
    {
        Testable::header('Content-type: application/json');

        if (
            $this->output instanceof JSONView\BaseROb &&
            $this->isResponseSubClass($this->output)
        ) {
            $this->output->setPretty($this->prettyPrint);
            $output = (string) $this->output;

            if ($this->errorResponse && $this->output instanceof JSONView\ROb) {
                $header = 'HTTP/1.1 '
                    .$this->output->getCode()
                    .' '
                    .$this->return_codes::getName($this->output->getCode())
                ;
                Testable::header($header);
            }

            if (false !== ($callback = $this->output->getJSONPCallback())) {
                $output = $callback.'('.$output.')';
            }

            echo $output;
        } else {
            if (is_array($this->output)) {
                foreach ($this->output as &$item) {
                    if ($item instanceof JSONView\BaseROb && $this->isResponseSubClass($item)) {
                        $item = json_decode((string) $item);
                    }
                }
            }

            if ($this->prettyPrint) {
                echo json_encode((array)$this->output, JSON_PRETTY_PRINT);
            } else {
                echo json_encode((array)$this->output);
            }
        }
    }


    public function onPreEvent(): void
    {
        $module = DI::getContainer()->get('Controller')->getModule();

        if ($this->config !== null) {
            if (count($this->config) === 1 && !isset($this->config[0])) {
                $this->config[0] = $this->config;
            }

            foreach ($this->config as $value) {
                if (in_array($module, array_keys($value), true)) {
                    $mod_conf = $value[$module];
                    $this->error_ob = $mod_conf['error_ob'] ?? \Empathy\MVC\Plugin\JSONView\EROb::class;
                    $this->return_ob = $mod_conf['return_ob'] ?? \Empathy\MVC\Plugin\JSONView\ROb::class;
                    $this->return_codes = $mod_conf['return_codes'] ?? \Empathy\MVC\Plugin\JSONView\ReturnCodes::class;
                    $this->prettyPrint = $mod_conf['pretty_print'] ?? false;

                    DI::getContainer()->get('PluginManager')->setView($this);
                }
            }
        }
    }

    public function exception(bool $debug, \Throwable $exception, bool $req_error): void
    {
        $rc = $this->return_codes;
        $e_ob = $this->error_ob;

        $code = $rc::Internal_Server_Error;

        if ($req_error) {
            switch ($exception->getCode()) {
                case RequestException::NOT_FOUND:
                    $code = $rc::Not_Found;
                    break;
                case RequestException::BAD_REQUEST:
                    $code = $rc::Bad_Request;
                    break;
                case RequestException::INTERNAL_ERROR:
                    $code = $rc::Internal_Server_Error;
                    break;
                case RequestException::NOT_AUTHENTICATED:
                    $code = $rc::Unauthorized;
                    break;
                case RequestException::NOT_AUTHORIZED:
                    $code = $rc::Forbidden;
                    break;
                case RequestException::METHOD_NOT_ALLOWED:
                    $code = $rc::Method_Not_Allowed;
                    break;
                default:
                    break;
            }
        }

        if ($debug || Config::get('BOOT_OPTIONS')['environment'] === 'dev') {
            $r = new $e_ob($code, 'Exception: ' .$exception->getMessage());
        } else {
            $r = new $e_ob($code, 'Server error.');
        }

        $header = 'HTTP/1.1 '
            .$code
            .' '
            .$this->return_codes::getName($code);
        Testable::header($header);

        $this->assign('default', $r, true);
        $this->display('');
    }


    public function getVars(): mixed
    {
        return $this->output;
    }

    public function clearVars(): void
    {
        $this->output = null;
    }
}
