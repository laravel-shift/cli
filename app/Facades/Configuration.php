<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, $default = null)
 * @method static array defaults()
 *
 * @see \App\Support\ConfigurationRepository
 */
class Configuration extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Configuration::class;
    }
}
