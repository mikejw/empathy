<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin\JSONView;

abstract class BaseROb
{
    private ?string $jsonp_callback = null;

    private bool $pretty = false;

    public function __construct()
    {
        $this->pretty = false;
    }

    public function setPretty(bool $pretty): void
    {
        $this->pretty = $pretty;
    }

    public function __toString(): string
    {
        $encoded = json_encode($this->serialize(), $this->pretty ? JSON_PRETTY_PRINT : 0);

        return $encoded === false ? '{}' : $encoded;
    }

    public function setJSONPCallback(?string $callback): void
    {
        $this->jsonp_callback = $callback;
    }

    public function getJSONPCallback(): string|false
    {
        if ($this->jsonp_callback !== null) {
            return $this->jsonp_callback;
        }

        return false;
    }

    /**
     * @return array<string, mixed>|\stdClass
     */
    abstract public function serialize(): array|\stdClass;
}
