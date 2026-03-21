<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

/**
 * Empathy Presentation interface
 * @file            Empathy/MVC/Plugin/Presentation.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
interface Presentation
{
    public function assign(string $name, mixed $data, bool $no_array = false): void;

    public function exception(bool $debug, \Throwable $exception, bool $req_error): void;

    public function display(string $template, bool $internal = false): void;

    public function getVars(): mixed;

    public function clearVars(): void;
}
