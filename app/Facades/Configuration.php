<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $key, $default = null)
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
