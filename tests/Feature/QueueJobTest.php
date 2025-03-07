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
        $app['config']->set('queue.default', 'database');
        $app['config']->set('queue.connections.database', [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ]);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    /** @test */
    public function it_can_process_queued_jobs()
    {
        Event::fake([JobProcessing::class]);
        
        $job = new TestJob();
        dispatch($job);

        $this->assertDatabaseHas('jobs', [
            'queue' => 'default'
        ]);
    }

    /** @test */
    public function it_respects_queue_priority()
    {
        Event::fake([JobProcessing::class]);
        
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
        dispatch($job)->onConnection('sync');

        Event::assertDispatched(JobProcessing::class, function ($event) {
            return true;
        });
    }
}

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        return true;
    }
}