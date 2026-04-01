<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Default PDO/RedBean connection settings read from merged application config ({@see Config}).
 *
 * @author Mike Whiting
 */
final readonly class DatabasePoolDefaults
{
    public function __construct(
        public string $server,
        public string $databaseName,
        public string $user,
        public string $password,
        public ?int $port,
    ) {
    }

    public static function tryFromConfig(): ?self
    {
        $server = Config::get('DB_SERVER');
        if ($server === false) {
            return null;
        }

        $name = Config::get('DB_NAME');
        $user = Config::get('DB_USER');
        $pass = Config::get('DB_PASS');
        $portRaw = Config::get('DB_PORT');
        $port = is_numeric($portRaw) ? (int) $portRaw : null;

        return new self(
            (string) $server,
            $name !== false ? (string) $name : '',
            $user !== false ? (string) $user : '',
            $pass !== false ? (string) $pass : '',
            $port,
        );
    }
}
