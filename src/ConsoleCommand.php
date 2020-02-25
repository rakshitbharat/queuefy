<?php


namespace Rakshitbharat\Queuefy;

use Illuminate\Console\Command;

class ConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queuefy:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run queue from console.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $commandToExecute = "";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $phpPath = exec("where php");
            $command = "tasklist | FIND ";
        } else {
            $phpPath = exec("which php");
            $command = "pgrep ";
        }


        if (
            empty(config('queuefy.QUEUE_COMMAND_FULL_PATH'))
            and
            !empty(config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN'))
        ) {
            $commandToExecute .= $phpPath . ' ';
            $commandToExecute .= base_path() . DIRECTORY_SEPARATOR . "artisan ";
            $commandToExecute .= config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN');
        }

        if (
            !empty(config('queuefy.QUEUE_COMMAND_FULL_PATH'))
            and
            empty(config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN'))
        ) {
            $commandToExecute .= config('queuefy.QUEUE_COMMAND_FULL_PATH');
        }
        $OUTPUT_FINAL = 'Nothing Done';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $commandToCheckProcess = $command . '"' . $commandToExecute . '"';
        } else {
            $commandToCheckProcess = $command . '"' . $commandToExecute . '"';
        }
        if (!($ID = shell_exec($commandToCheckProcess))) {
            $ID = trim($ID);
            $OUTPUT_FINAL = 'Process is running with PID ' . $ID . '.';
        }
        if(empty($ID)){
            $OUTPUT_FINAL = shell_exec($commandToExecute);
        }
        echo $OUTPUT_FINAL;
    }
}
