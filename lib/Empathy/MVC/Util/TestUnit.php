<?php

require 'PHPUnit/Extensions/OutputTestCase.php';
require 'Empathy/Util/Test.php';

class TestUnit extends \PHPUnit_Framework_TestCase
{
    private $test;
    protected $controller;

    protected function setUp()
    {
        // create new Test object
        // passing front controller path
        // and setting output mode to false
        $this->test = new Test($this->controller, false);
    }

    protected function setRequest($req)
    {
        // set Test object request string
        $this->test->setRequest($req);
    }

    protected function doRequest()
    {
        $this->test->process();
    }

    protected function getResponse()
    {
        return $this->test->getStatus();
    }

}
