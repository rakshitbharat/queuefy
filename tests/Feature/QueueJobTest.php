<?php

namespace Rakshitbharat\Queuefy\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Rakshitbharat\Queuefy\QueuefyServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueJobTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [QueuefyServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        // Use database queue for proper event testing
        $app['config']->set('queue.default', 'database');
        $app['config']->set('queue.connections.database', [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ]);
        $app['config']->set('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN', 'queue:work --timeout=0');
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create jobs table
        $this->artisan('queue:table');
        $this->artisan('migrate');
        
        // Clear any existing events/jobs
        Event::fake();
    }

    protected function tearDown(): void
    {
        $this->artisan('migrate:reset');
        parent::tearDown();
    }

    /** @test */
    public function it_can_process_queued_jobs()
    {
        $job = new TestJob();
        dispatch($job);

        // Assert job was pushed to queue
        $this->assertDatabaseHas('jobs', [
            'queue' => 'default'
        ]);
    }

    /** @test */
    public function it_respects_queue_priority()
    {
        $highPriorityJob = (new TestJob())->onQueue('high');
        $defaultPriorityJob = new TestJob();

        dispatch($highPriorityJob);
        dispatch($defaultPriorityJob);

        $this->assertDatabaseHas('jobs', ['queue' => 'high']);
        $this->assertDatabaseHas('jobs', ['queue' => 'default']);
    }

    /** @test */
    public function it_handles_job_events()
    {
        Event::fake([JobProcessing::class]);
        
        $job = new TestJob();
        dispatch($job)->onConnection('sync'); // Use sync for immediate processing

        Event::assertDispatched(JobProcessing::class);
    }
}

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Simulate job work
        usleep(100);
        return true;
    }
}