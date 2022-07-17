<?php

namespace Empathy\MVC\Plugin\JSONView;


class ROb extends BaseROb
{
    protected $meta;
    protected $data;
    protected $pagination;
    
    public function __construct()
    {
        $this->meta = new \stdClass();
        $this->data = new \stdClass();
        $this->pagination = new \stdClass();
        $this->meta = new \stdClass();
        $this->meta->code = ReturnCodes::OK;
    }

    public function setData($data) 
    {
        $this->data = $data;
    }

    public function setPagination($pagination) 
    {
        $this->pagination = $pagination;
    }
    
    public function getCode()
    {
        return $this->meta->code;
    }

    public function serialize() 
    {
        $ob = new \stdClass();
        $ob->meta = $this->meta;
        $ob->data = $this->data;
        $ob->pagination = $this->pagination;
        return $ob;
    }
}
