<?php

declare(strict_types=1);

if (!class_exists('Smarty')) {
    class Smarty
    {
        /** @var bool */
        public $debugging = false;

        /** @var int|bool */
        public $caching = false;

        /** @var string */
        public $compile_dir = '';

        /** @var string */
        public $cache_dir = '';

        /** @var string */
        public $config_dir = '';

        /** @var int */
        public $error_reporting = 0;

        /** @var mixed */
        public $template_dir;

        /**
         * @param mixed $value
         */
        public function assign(string $name, $value): void
        {
        }

        public function clear_assign(string $name): void
        {
        }

        public function display(string $template): void
        {
        }

        /**
         * @param string $template
         * @return string
         */
        public function fetch(string $template): string
        {
            return '';
        }

        public function load_filter(string $type, string $name): void
        {
        }

        /**
         * @param mixed $callback
         */
        public function registerResource(string $name, $callback): void
        {
        }

        /**
         * @param mixed $callback
         * @param mixed $cacheable
         * @param mixed $cacheAttrs
         */
        public function registerPlugin(string $type, string $name, $callback, $cacheable = null, $cacheAttrs = null): void
        {
        }

        /**
         * @return mixed
         */
        public function getTemplateVars(?string $name = null)
        {
        }

        public function clear_all_assign(): void
        {
        }
    }
}
