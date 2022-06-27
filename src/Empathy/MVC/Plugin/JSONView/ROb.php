<?php
namespace Empathy\MVC\Plugin\JSONView;
use Empathy\MVC\Plugin\JSONView\ReturnCodes as RC;


class ROb
{
    protected $meta;
    protected $data;
    protected $pagination;
    private $jsonp_callback;

    public function __construct()
    {
        $this->meta = new \stdClass();
        $this->meta->code = RC::OK;

        $this->data = new \stdClass();
        $this->pagination = new \stdClass();
    }

    public function __call($name, $args)
    {
        if (strpos($name, 'get') === 0) {
            $prop = strtolower(substr($name, 3));

            return $this->$prop;
        } elseif (strpos($name, 'set') === 0) {
            $prop = strtolower(substr($name, 3));
            $this->$prop = $args[0];
        }
    }

    public function __toString()
    {        
        $ob = new \stdClass();
        $ob->meta = $this->meta;
        $ob->data = $this->data;
        $ob->pagination = $this->pagination;

        return json_encode($ob);
    }

    public function setJSONPCallback($callback) {
        $this->jsonp_callback = $callback;
    }

    public function getJSONPCallback() {
        $callback = false;
        if($this->jsonp_callback !== null) {
            $callback = $this->jsonp_callback;
        }
        return $callback;
    }

    public function getCode()
    {
        return $this->meta->code;
    }
}
