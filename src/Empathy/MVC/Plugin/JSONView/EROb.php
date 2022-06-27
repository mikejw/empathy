<?php

namespace Empathy\MVC\Plugin\JSONView;

class EROb extends ROb
{

    public function __construct($code, $message, $type='')
    {
        parent::__construct();
        $this->meta->code = $code;
        $this->meta->error_message = $message;

    }
}
