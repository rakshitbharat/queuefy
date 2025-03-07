<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsoleCommand extends Command
{
    protected $signature = 'queuefy:run';
    protected $description = 'Run queue worker through cron';
    
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
            $this->logMessage('Queue processing stopped via STOP_QUEUE configuration');
            return;
        }

        $commandToExecute = $this->buildCommand();
        
        if (empty($commandToExecute)) {
            $this->error('No valid queue command configuration found');
            $this->logMessage('Failed to start queue worker: No valid command configuration');
            return;
        }

        if ($this->isProcessRunning($commandToExecute)) {
            $this->info('Queue worker is already running');
            $this->logMessage('Queue worker already running - skipping start');
            return;
        }

        $this->startQueueWorker($commandToExecute);
        $this->info('Queue worker started successfully');
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

    // Changed from private to protected so we can mock it in tests
    protected function isProcessRunning(string $command): bool
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
            
            $this->logMessage('Started queue worker with command: ' . $command);
        } catch (\Exception $e) {
            $this->error('Failed to start queue worker: ' . $e->getMessage());
            $this->logMessage('Failed to start queue worker: ' . $e->getMessage());
            throw $e;
        }
    }

    private function logMessage(string $message): void 
    {
        if (config('queuefy.QUEUE_LOG_QUE_COMMAND_FIRED', false)) {
            Log::info('Queuefy: ' . $message);
        }
    }
}
