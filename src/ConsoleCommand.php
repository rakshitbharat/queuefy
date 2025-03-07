<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsoleCommand extends Command
{
    protected $signature = 'queuefy:run';
    protected $description = 'Run Laravel queue worker through cron';
    
    private $isWindows;

    public function __construct()
    {
        parent::__construct();
        $this->isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function handle()
    {
        if (config('queuefy.STOP_QUEUE', false)) {
            $this->info('Queue processing is stopped via STOP_QUEUE configuration');
            return;
        }

        $commandToExecute = $this->buildCommand();
        
        if (empty($commandToExecute)) {
            $this->error('No valid queue command configuration found');
            return;
        }

        if ($this->isProcessRunning($commandToExecute)) {
            $this->info('Queue worker is already running');
            return;
        }

        $this->startQueueWorker($commandToExecute);
    }

    private function buildCommand(): string
    {
        $phpPath = $this->isWindows ? exec("where php") : exec("which php");
        $customCommand = config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN');
        
        if (empty($customCommand)) {
            return '';
        }

        return sprintf(
            '%s %s/artisan %s',
            $phpPath,
            base_path(),
            $customCommand
        );
    }

    private function isProcessRunning(string $command): bool
    {
        $checkCommand = $this->isWindows
            ? sprintf('tasklist | FIND "%s"', $command)
            : sprintf('pgrep -f "%s"', $command);

        $result = shell_exec($checkCommand);
        
        return !empty(trim($result));
    }

    private function startQueueWorker(string $command)
    {
        try {
            if ($this->isWindows) {
                pclose(popen('start /B ' . $command, 'r'));
            } else {
                shell_exec($command . ' > /dev/null 2>&1 &');
            }

            $this->info('Queue worker started successfully');
            
            if (config('queuefy.QUEUE_LOG_QUE_COMMAND_FIRED', false)) {
                Log::info('Queuefy: Started queue worker with command: ' . $command);
            }
        } catch (\Exception $e) {
            $this->error('Failed to start queue worker: ' . $e->getMessage());
            Log::error('Queuefy: Failed to start queue worker - ' . $e->getMessage());
        }
    }
}
