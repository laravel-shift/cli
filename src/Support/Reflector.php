<?php

namespace Shift\Cli\Support;

use Illuminate\Support\Arr;

class Reflector
{
    private static bool $autoloaded = false;

    private string $cwd;

    public function __construct(string $cwd)
    {
        $this->cwd = $cwd;

        if (! self::$autoloaded) {
            $this->autoloadProject();
        }
    }

    public function classFromPath(string $path): ?\ReflectionClass
    {
        $fqcn = $this->fqcnFromPath($path);
        if (is_null($fqcn)) {
            return null;
        }

        try {
            $class = new \ReflectionClass($fqcn);
        } catch (\ReflectionException) {
            return null;
        }

        return $class;
    }

    private function autoloadProject(): void
    {
        self::$autoloaded = true;

        if (file_exists($this->cwd . '/vendor/autoload.php')) {
            require_once $this->cwd . '/vendor/autoload.php';
        }
    }

    private function findNamespace(string $path): ?array
    {
        $namespaces = $this->psr4Namespaces();

        foreach ($namespaces as $namespace => $sources) {
            foreach (Arr::wrap($sources) as $source) {
                if (str_starts_with($path, $source)) {
                    return [$source, $namespace];
                }
            }
        }

        return null;
    }

    private function psr4Namespaces(): array
    {
        static $namespaces;

        if (is_null($namespaces)) {
            $composer = json_decode(file_get_contents('composer.json'), true);
            $namespaces = $composer['autoload']['psr-4'] ?? [];
        }

        return $namespaces;
    }

    private function fqcnFromPath($path): ?string
    {
        if (str_starts_with($path, $this->cwd . DIRECTORY_SEPARATOR)) {
            $path = substr($path, strlen($this->cwd . DIRECTORY_SEPARATOR));
        }

        [$source, $namespace] = $this->findNamespace($path);

        if (is_null($source)) {
            return null;
        }

        return str_replace(
            [$source, DIRECTORY_SEPARATOR],
            [$namespace, '\\'],
            substr($path, 0, -4)
        );
    }
}
