<?php

namespace Empathy\MVC\Plugin\JSONView;


class EROb extends ROb
{

    public function __construct($code, $message)
    {
        parent::__construct();
        $this->meta = new \stdClass();
        $this->meta->code = $code;
        $this->meta->error_message = $message;
    }

    public static function getObject($code)
    {    
        return new EROb($code, ReturnCodes::getName($code));
    }
}
