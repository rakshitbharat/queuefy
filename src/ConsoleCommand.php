<?php


namespace Rakshitbharat\Queuefy;

use Illuminate\Console\Command;

class ConsoleCommand extends Console
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
        if (!($PID = shell_exec("pgrep 'php /testcron.php'")))
            echo 'Process is running with PID ' . $PID . '.';

    }
}
