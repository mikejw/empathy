<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin\JSONView;

class ROb extends BaseROb
{
    protected \stdClass $meta;

    protected mixed $data;

    protected mixed $pagination;

    public function __construct()
    {
        parent::__construct();
        $this->meta = new \stdClass();
        $this->data = new \stdClass();
        $this->pagination = new \stdClass();
        $this->meta->code = ReturnCodes::OK;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function setPagination(mixed $pagination): void
    {
        $this->pagination = $pagination;
    }

    public function getCode(): int
    {
        return (int) $this->meta->code;
    }

    public function serialize(): \stdClass
    {
        $ob = new \stdClass();
        $ob->meta = $this->meta;
        $ob->data = $this->data;
        $ob->pagination = $this->pagination;
        return $ob;
    }
}
