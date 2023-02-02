<?php

namespace Empathy\MVC\Plugin;
use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\Testable;
use Empathy\MVC\Config;
use Empathy\MVC\RequestException;


/**
 * Empathy JSONView Plugin
 * @file            Empathy/MVC/Plugin/JSONView.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class JSONView extends Plugin implements PreEvent, Presentation
{
    private $output;
    private $error_ob;
    private $return_ob;
    private $return_codes;
    private $prettyPrint;
    private $errorResponse = false;


    public function assign($name, $data, $no_array = false)
    {
        if ($no_array) {
            $this->output = $data;
        } else {
            if (isset($this->output) && is_object($this->output)) {
                $this->clearVars();
            }
            $this->output[$name] = $data;
        }
    }


    private function isResponseSubClass($object) {
        return (
            (
                $this->errorResponse = (get_class($object) == $this->error_ob ||
                is_subclass_of($object, $this->error_ob))
            ) ||
            (
                get_class($object) == $this->return_ob ||
                is_subclass_of($object, $this->return_ob)
            )
        );
    }

    public function display($template, $internal = false)
    {
        Testable::header('Content-type: application/json');

        if (
            is_object($this->output) &&
            $this->isResponseSubClass($this->output)
        ) {
            $this->output->setPretty($this->prettyPrint);
            $output = (string) $this->output;

            if ($this->errorResponse) {
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
                    if (is_object($item) && $this->isResponseSubClass($item)) {
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


    public function onPreEvent()
    {
        $module = $this->bootstrap->getController()->getModule();

        if (isset($this->config)) {
            if (count($this->config) === 1 && !isset($this->config[0])) {
                $this->config[0] = $this->config;
            }

            foreach ($this->config as $item => $value) {
                if (in_array($module, array_keys($value))) {
                    $mod_conf = $value[$module];
                    $this->error_ob = isset($mod_conf['error_ob'])
                        ? $mod_conf['error_ob']
                        : 'Empathy\MVC\Plugin\JSONView\EROb';
                    $this->return_ob = isset($mod_conf['return_ob'])
                        ? $mod_conf['return_ob']
                        : 'Empathy\MVC\Plugin\JSONView\ROb';
                    $this->return_codes = isset($mod_conf['return_codes'])
                        ? $mod_conf['return_codes']
                        : 'Empathy\MVC\Plugin\JSONView\ReturnCodes';
                    $this->prettyPrint = isset($mod_conf['pretty_print'])
                        ? $mod_conf['pretty_print']
                        : false;
                    $controller = $this->bootstrap->getController();
                    $controller->setPresenter($this);
                }
            }
        }
    }

    public function exception($debug, $exception, $req_error)
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

        if ($debug || Config::get('BOOT_OPTIONS')['environment'] == 'dev') {
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

   
    public function getVars()
    {
        return $this->output;
    }

    public function clearVars()
    {
        unset($this->output);
    }
}
