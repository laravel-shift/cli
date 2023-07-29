<?php

namespace Shift\Cli\Providers;

use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use Shift\Cli\Support\TaskManifest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TaskManifest::class, function () {
            return new TaskManifest(Env::get('COMPOSER_VENDOR_DIR') ?: getcwd() . '/vendor');
        });
    }
}
