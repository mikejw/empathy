<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Per-dispatch (request-scoped) state: current URI instance and controller.
 *
 * Reset at the start of each {@see Bootstrap::dispatch()} so persistent / CLI reuse
 * does not leak the previous request’s objects. The DI container may still register
 * the active controller for backward compatibility; this object is the framework’s
 * explicit source of truth for “what is being dispatched now”.
 */
final class DispatchContext
{
    private ?URI $uri = null;

    private ?Controller $controller = null;

    /** @var array<string, mixed> */
    private array $attributes = [];

    /**
     * Clear dispatch-scoped state before building a new route + controller.
     */
    public function reset(): void
    {
        $this->uri = null;
        $this->controller = null;
        $this->attributes = [];
    }

    public function setUri(?URI $uri): void
    {
        $this->uri = $uri;
    }

    public function getUri(): ?URI
    {
        return $this->uri;
    }

    public function setController(?Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function getController(): ?Controller
    {
        return $this->controller;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : $default;
    }
}
