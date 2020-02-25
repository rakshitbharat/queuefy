<?php

namespace Rakshitbharat\Queuefy;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rakshitbharat\Queuefy\Skeleton\SkeletonClass
 */
class QueuefyFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'queuefy';
    }
}
