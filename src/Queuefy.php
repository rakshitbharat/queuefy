<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Support\Facades\Artisan;

class Queuefy
{
    /**
     * Start the queue worker
     *
     * @return void
     */
    public function start()
    {
        Artisan::call('queuefy:run');
    }

    /**
     * Stop the queue worker by setting STOP_QUEUE to true
     *
     * @return void
     */
    public function stop()
    {
        config(['queuefy.STOP_QUEUE' => true]);
    }

    /**
     * Check if the queue worker is running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $customCommand = config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN');
        if (empty($customCommand)) {
            return false;
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $command = $isWindows
            ? sprintf('tasklist | FIND "%s"', $customCommand)
            : sprintf('pgrep -f "%s"', $customCommand);

        return !empty(trim(shell_exec($command)));
    }

    /**
     * Get the current queue command being used
     *
     * @return string|null
     */
    public function getCurrentCommand(): ?string
    {
        return config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN');
    }
}
