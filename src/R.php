<?php

declare(strict_types=1);

namespace {
    if (class_exists(\RedBeanPHP\Facade::class)) {
        /**
         * @method static void setup(string $dsn, ?string $username = null, ?string $password = null, bool $frozen = false)
         * @method static bool testConnection()
         * @method static mixed dispense(string $type, int $number = 1)
         * @method static mixed findOne(string $type, string $sql = null, array<string, mixed> $bindings = [])
         * @method static mixed store($bean)
         * @method static mixed load(string $type, int|string $id)
         * @method static void close()
         */
        class R extends \RedBeanPHP\Facade
        {
            //
        }
    }
}
