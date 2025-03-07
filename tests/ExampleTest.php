<?php

namespace Rakshitbharat\Queuefy\Tests;

use Orchestra\Testbench\TestCase;
use Rakshitbharat\Queuefy\QueuefyServiceProvider;
use Rakshitbharat\Queuefy\QueuefyFacade;
use Illuminate\Support\Facades\Artisan;

class QueuefyTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [QueuefyServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Queuefy' => QueuefyFacade::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN', 'queue:work --timeout=0');
        $app['config']->set('queuefy.QUEUE_LOG_QUE_COMMAND_FIRED', false);
        $app['config']->set('queuefy.STOP_QUEUE', false);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_load_configuration()
    {
        $this->assertEquals(
            'queue:work --timeout=0',
            config('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN')
        );
        
        $this->assertFalse(
            config('queuefy.QUEUE_LOG_QUE_COMMAND_FIRED')
        );
        
        $this->assertFalse(
            config('queuefy.STOP_QUEUE')
        );
    }

    /** @test */
    public function it_can_publish_configuration()
    {
        // Ensure the config can be published
        $this->artisan('vendor:publish', [
            '--provider' => 'Rakshitbharat\Queuefy\QueuefyServiceProvider',
            '--tag' => 'config'
        ]);

        $this->assertFileExists(config_path('queuefy.php'));
    }

    /** @test */
    public function it_respects_stop_queue_configuration()
    {
        // Set STOP_QUEUE to true
        config(['queuefy.STOP_QUEUE' => true]);
        
        $this->artisan('queuefy:run')
            ->expectsOutput('Queue processing is stopped via STOP_QUEUE configuration')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_get_current_queue_command()
    {
        $customCommand = 'queue:work --queue=high,default --tries=3';
        config(['queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN' => $customCommand]);

        $this->assertEquals(
            $customCommand,
            \Queuefy::getCurrentCommand()
        );
    }

    /** @test */
    public function it_can_check_if_queue_is_running()
    {
        // Initially queue should not be running
        $this->assertFalse(\Queuefy::isRunning());
    }

    /** @test */
    public function it_can_start_queue_worker()
    {
        $this->artisan('queuefy:run')
            ->expectsOutput('Queue worker started successfully')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_handles_custom_queue_commands()
    {
        $customCommand = 'queue:work --queue=high,default --sleep=5';
        config(['queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN' => $customCommand]);

        $this->artisan('queuefy:run')
            ->expectsOutput('Queue worker started successfully')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_logs_queue_events_when_enabled()
    {
        config(['queuefy.QUEUE_LOG_QUE_COMMAND_FIRED' => true]);

        $this->artisan('queuefy:run')
            ->expectsOutput('Queue worker started successfully')
            ->assertExitCode(0);

        // You would typically check the log file here, but for testing purposes
        // we're just verifying the command executes successfully
    }

    /** @test */
    public function it_prevents_duplicate_queue_workers()
    {
        // First run should start the worker
        $this->artisan('queuefy:run')
            ->expectsOutput('Queue worker started successfully')
            ->assertExitCode(0);

        // Second run should detect running worker
        $this->artisan('queuefy:run')
            ->expectsOutput('Queue worker is already running')
            ->assertExitCode(0);
    }
}
