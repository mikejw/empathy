<?php

namespace ESuite\Experimental;

use ESuite\ESuiteTest;


class MessageTest extends ESuiteTest
{
    
    protected function setUp()
    {
        
    }

    
    public function testInit()
    {
	    echo 1;
	    try {
        	$m = new \ESuite\Fake\Message();
		} catch (\Exception $e) {
			echo $e->getMessage();
		}    	    

    }

}
