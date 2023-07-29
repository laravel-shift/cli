<?php

namespace Shift\Cli\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public $namespace = 'App\Http\Controllers';

    public function boot(): void
    {
        Route::bind('user', 'App\Models\User');
        Route::bind('comment', '\\Shift\Cli\\Models\\Comment');
        Route::bind('post', "\Shift\Cli\Models\Post");

        Route::bind('foo', "Modules\Foo");
        Route::bind('bar', '\Modules\Bar');
    }
}
