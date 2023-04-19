<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static get(string $key, $default)
 */
class Configuration extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Configuration::class;
    }
}
