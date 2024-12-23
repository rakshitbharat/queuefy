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
                $queueCommandAfterPhpArtisan = config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN');
                $logQueCommandFired = config('queuefy.QUEUE_LOG_QUE_COMMAND_FIRED');

                if (!empty($queueCommandAfterPhpArtisan)) {
                    $schedule = app(Schedule::class);
                    $schedule->command('queuefy:run')->everyMinute();

                    // Log the scheduled command

                    if ($logQueCommandFired) {
                        \Log::info('Queuefy command scheduled: queuefy:run');
                    }
                } else {
                    // Log the reason for not scheduling the command
                    \Log::warning('Queuefy command not scheduled: QUEUE_COMMAND_AFTER_PHP_ARTISAN is empty');
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
