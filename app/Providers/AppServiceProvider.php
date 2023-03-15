<?php

namespace App\Providers;

use App\Support\TaskManifest;
use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TaskManifest::class, function () {
            return new TaskManifest(Env::get('COMPOSER_VENDOR_DIR') ?: getcwd() . '/vendor');
        });
    }
}
