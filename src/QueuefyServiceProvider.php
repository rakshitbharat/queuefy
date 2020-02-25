<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Support\ServiceProvider;

class QueuefyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('queuefy.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'queuefy');

        // Register the main class to use with the facade
        $this->app->singleton('queuefy', function () {
            return new Queuefy;
        });
    }
}
