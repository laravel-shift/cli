<?php

namespace App\Providers;

use App\Facades\Comment;
use App\Facades\Configuration;
use App\Support\CommentRepository;
use App\Support\ConfigurationRepository;
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

        $this->app->singleton(Configuration::class, ConfigurationRepository::class);
        $this->app->singleton(Comment::class, CommentRepository::class);
    }
}
