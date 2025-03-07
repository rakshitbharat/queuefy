<?php

namespace Rakshitbharat\Queuefy\Tests;

use Orchestra\Testbench\TestCase;
use Rakshitbharat\Queuefy\QueuefyServiceProvider;
use Rakshitbharat\Queuefy\QueuefyFacade;
use Rakshitbharat\Queuefy\ConsoleCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Mockery;

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
        
        // Reset Mockery after each test
        Mockery::close();
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
        $this->artisan('vendor:publish', [
            '--provider' => 'Rakshitbharat\Queuefy\QueuefyServiceProvider',
            '--tag' => 'config'
        ])->assertExitCode(0);
    }

    /** @test */
    public function it_respects_stop_queue_configuration()
    {
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
        $this->assertFalse(\Queuefy::isRunning());
    }

    /** @test */
    public function it_can_start_queue_worker()
    {
        $mock = $this->createMockCommand();
        $mock->shouldReceive('isProcessRunning')->once()->andReturn(false);
        
        $this->app->instance(ConsoleCommand::class, $mock);

        $this->artisan('queuefy:run')
            ->assertSuccessful();
    }

    /** @test */
    public function it_handles_custom_queue_commands()
    {
        $customCommand = 'queue:work --queue=high,default --sleep=5';
        config(['queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN' => $customCommand]);

        $mock = $this->createMockCommand();
        $mock->shouldReceive('isProcessRunning')->once()->andReturn(false);
        
        $this->app->instance(ConsoleCommand::class, $mock);

        $this->artisan('queuefy:run')
            ->assertSuccessful();
    }

    /** @test */
    public function it_logs_queue_events_when_enabled()
    {
        config(['queuefy.QUEUE_LOG_QUE_COMMAND_FIRED' => true]);

        Log::shouldReceive('info')
           ->once()
           ->withArgs(function($message) {
               return str_starts_with($message, 'Queuefy:');
           });

        $mock = $this->createMockCommand();
        $mock->shouldReceive('isProcessRunning')->once()->andReturn(false);
        
        $this->app->instance(ConsoleCommand::class, $mock);

        $this->artisan('queuefy:run')
            ->assertSuccessful();
    }

    /** @test */
    public function it_prevents_duplicate_queue_workers()
    {
        $mock = $this->createMockCommand();
        // First call returns false (not running), second call returns true (running)
        $mock->shouldReceive('isProcessRunning')->twice()->andReturn(false, true);
        
        $this->app->instance(ConsoleCommand::class, $mock);

        // First run should start the worker
        $this->artisan('queuefy:run')
            ->assertSuccessful();

        // Second run should detect running worker
        $this->artisan('queuefy:run')
            ->expectsOutput('Queue worker is already running')
            ->assertSuccessful();
    }

    /**
     * Create a properly initialized mock command
     */
    protected function createMockCommand()
    {
        $mock = Mockery::mock(ConsoleCommand::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        
        $mock->__construct(); // Call the constructor manually
        
        return $mock;
    }
}
