<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class QueuefyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ConsoleCommand::class
            ]);
            $this->app->booted(function () {
                if (
                    !empty(config('queuefy.STOP_QUEUE'))
                    and
                    !empty(config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN'))
                    and
                    config('queuefy.STOP_QUEUE') == false
                ) {
                    $schedule = app(Schedule::class);
                    $schedule->command('queuefy:run')->everyMinute();
                }
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'queuefy');

        // Register the main class to use with the facade
        $this->app->singleton('queuefy', function () {
            return new Queuefy;
        });
    }
}
