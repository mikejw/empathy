<?php

define('TEST_APP', '/var/www/sites/eaa');

date_default_timezone_set('Europe/London');


require('Empathy/Util/TestUnit.php');


class TestApp extends TestUnit
{
  protected $controller;

  protected function setUp()
  {
    $this->controller = TEST_APP.'/public_html/index.php';
    parent::setUp();
  }
  
  public function testDefault()
  {    
    $this->setRequest('blah');
    $this->doRequest();

    $resp = $this->getResponse();
    $matches = preg_grep('/Error found/', explode('\n', $resp));

    $this->assertTrue(sizeof($matches) == 1);     
  }
}
?>
