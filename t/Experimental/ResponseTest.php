<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


class ResponseTest extends ESuiteTest
{
    private $response;
    
    protected function setUp(): void
    {
        $this->response = new \ESuite\Fake\Response();
    }

    public function testGetStatusCode()
    {
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testWithStatus()
    {
        $status = 404;
        $r = $this->response->withStatus($status);
        $this->assertInstanceOf('\ESuite\Fake\Response', $r);
        $this->assertEquals($status, $r->getStatusCode());

    }

    public function testGetReasonPhrase()
    {
        $status = 404;
        $r = $this->response->withStatus($status, 'some reason');
        $this->assertEquals('some reason', $r->getReasonPhrase());
    }

}

