<?php

namespace Shift\Cli\Facades;

/**
 * @method static \ReflectionClass|null classFromPath(string $path)
 *
 * @see \Shift\Cli\Support\Reflector
 */
class Reflector extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return static::class;
    }

    protected static function getInstance()
    {
        return new \Shift\Cli\Support\Reflector(getcwd());
    }
}
