<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin\JSONView;

class EROb extends ROb
{
    public function __construct(int $code, string $message)
    {
        parent::__construct();
        $this->meta = new \stdClass();
        $this->meta->code = $code;
        $this->meta->error_message = $message;
    }

    public static function getObject(int $code): self
    {
        return new self($code, ReturnCodes::getName($code));
    }
}
