<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Immutable routing and document paths.
 *
 * Built from {@see Config} via {@see self::fromConfig}. Resolution is not cached
 * on {@see Empathy} so tests can {@see Config::store} after the application
 * object is constructed and still receive up-to-date paths from
 * {@see Empathy::getApplicationPaths()}.
 *
 * @author Mike Whiting
 */
final readonly class ApplicationPaths
{
    public function __construct(
        public ?string $webRoot,
        public ?string $publicDir,
        public ?string $docRoot,
        public string $configDir,
    ) {
    }

    public static function fromConfig(string $configDir): self
    {
        $w = Config::get('WEB_ROOT');
        $p = Config::get('PUBLIC_DIR');
        $d = Config::get('DOC_ROOT');

        return new self(
            $w !== false ? (string) $w : null,
            $p !== false ? (string) $p : null,
            $d !== false ? (string) $d : null,
            $configDir,
        );
    }
}
