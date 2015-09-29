<?php

namespace ESuite\Fake;

use Psr\Http\Message\ResponseInterface;


class Response extends Message implements ResponseInterface
{
    private $status;
    private $reason = '';


    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setReason($reason)
    {
        $this->status = $status;
    }


    // interface methods


    public function getStatusCode()
    {
        $this->status = 200;
        return $this->status;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $r = clone $this;
        $r->status = $code;
        if ($reasonPhrase != '' ) {
            $r->setReason($reasonPhrase);
        }
        return $r;
    }

    public function getReasonPhrase()
    {
        return $this->reason;
    }

}
