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
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN', 'queue:work --timeout=0');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake([JobProcessing::class]);
    }

    /** @test */
    public function it_can_process_queued_jobs()
    {
        $job = new TestJob();
        dispatch($job);

        Queue::assertPushed(TestJob::class);
    }

    /** @test */
    public function it_respects_queue_priority()
    {
        config(['queuefy.QUEUE_COMMAND_AFTER_PHP_ARTISAN' => 'queue:work --queue=high,default']);

        $highPriorityJob = (new TestJob())->onQueue('high');
        $defaultPriorityJob = new TestJob();

        dispatch($highPriorityJob);
        dispatch($defaultPriorityJob);

        Queue::assertPushed(TestJob::class, function ($job) {
            return $job->queue === 'high';
        });

        Queue::assertPushed(TestJob::class, function ($job) {
            return $job->queue === null;
        });
    }

    /** @test */
    public function it_handles_job_events()
    {
        $job = new TestJob();
        dispatch($job);

        Event::assertDispatched(JobProcessing::class);
        Queue::assertPushed(TestJob::class);
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