<?php

namespace ESuite\Fake;

use Psr\Http\Message\ResponseInterface;


class Response extends Message implements ResponseInterface
{
    private $status = 200;
    private $reason = '';


    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;
    }


    // interface methods


    public function getStatusCode()
    {
        return $this->status;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $r = clone $this;
        $r->setStatus($code);
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
