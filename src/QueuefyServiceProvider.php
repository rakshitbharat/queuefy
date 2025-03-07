<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

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

            // Allow package config to be published
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('queuefy.php'),
            ], 'config');

            $this->app->booted(function () {
                $this->scheduleQueueCommand();
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Merge package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'queuefy');

        // Register singleton
        $this->app->singleton('queuefy', function () {
            return new Queuefy;
        });
    }

    /**
     * Schedule the queue command based on configuration
     */
    protected function scheduleQueueCommand()
    {
        $queueCommand = config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN');
        $logEnabled = config('queuefy.QUEUE_LOG_QUE_COMMAND_FIRED', false);
        $stopQueue = config('queuefy.STOP_QUEUE', false);

        if ($stopQueue) {
            if ($logEnabled) {
                Log::info('Queuefy: Queue processing is stopped via STOP_QUEUE configuration');
            }
            return;
        }

        if (empty($queueCommand)) {
            Log::warning('Queuefy: Queue command not scheduled - QUEUE_COMMAND_AFTER_PHP_ARTISAN is empty');
            return;
        }

        try {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('queuefy:run')
                    ->everyMinute()
                    ->withoutOverlapping()
                    ->runInBackground();

            if ($logEnabled) {
                Log::info('Queuefy: Queue command scheduled successfully - ' . $queueCommand);
            }
        } catch (\Exception $e) {
            Log::error('Queuefy: Failed to schedule queue command - ' . $e->getMessage());
        }
    }
}
