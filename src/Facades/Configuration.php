<?php

namespace Shift\Cli\Facades;

use Shift\Cli\Support\ConfigurationRepository;

/**
 * @method static mixed get(string $key, $default = null)
 * @method static array defaults()
 *
 * @see \Shift\Cli\Support\ConfigurationRepository
 */
class Configuration extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Configuration::class;
    }

    protected static function getInstance()
    {
        return new ConfigurationRepository();
    }
}
