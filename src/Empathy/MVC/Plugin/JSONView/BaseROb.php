<?php

namespace Empathy\MVC\Plugin\JSONView;


abstract class BaseROb
{
	private $jsonp_callback;
    private $pretty;


	public function __construct()
    {
        $this->pretty = false;        
    }

	public function setPretty($pretty)
    {
        $this->pretty = $pretty;
    }

	public function __toString()
    {        
        return json_encode($this->serialize(), $this->pretty ? JSON_PRETTY_PRINT : 0);
    }

    public function setJSONPCallback($callback) 
    {
        $this->jsonp_callback = $callback;
    }

    public function getJSONPCallback() 
    {
        $callback = false;
        if ($this->jsonp_callback !== null) {
            $callback = $this->jsonp_callback;
        }
        return $callback;
    }

    abstract public function serialize();
}
