<?php

namespace Empathy\MVC\Util;

/*
  usage:
  <?php
  include 'Empathy/Util/Test.php';
  $t = new Test('/var/www/proper/public_html/index.php');
  $req = 'blah';
  $t->setRequest($req, false);
  $t->process();
  echo $t;
  ?>
*/

class Test
{
    private $request;
    private $response;
    private $t_request_start;
    private $t_request_finish;
    private $t_elapsed;
    private $front_controller;
    private $output;

    public function __construct($front, $output = false)
    {
        $this->front_controller = $front;
        $this->output = $output;
    }

    public function __toString()
    {
        if ($this->output) {
            return $this->getResponse();
        } else {
            return $this->getStatus();
        }
    }

    public function setRequest($req)
    {
        $this->request = $req;
    }

    public function process()
    {
        ob_start();
        $this->t_request_start = microtime();
        $_SERVER['REQUEST_URI'] = $this->request;
        @include($this->front_controller);
        $this->t_request_finish = microtime();
        $this->response = ob_get_contents();
        ob_end_clean();
        $this->t_elapsed = ($this->t_request_finish - $this->t_request_start);
    }

    public function getStatus()
    {
        $error = '';
        if (strpos($this->response, '<!-- MVC Version')) {
            $error = 'Error found';
        }

        $status = 'Request was deat with in '.$this->t_elapsed.' microseconds...'."\n";
        $status .= 'Error information: '.$error."\n";

        return $status;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
