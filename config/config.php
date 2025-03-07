<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Queue Command Configuration
    |--------------------------------------------------------------------------
    |
    | This value determines the command that will be executed after 'php artisan'.
    | For example, if you set this to "queue:work --timeout=0", the full command
    | will be "php artisan queue:work --timeout=0"
    |
    */
    'QUEUE_COMMAND_AFTER_PHP_ARTISAN' => env('QUEUE_COMMAND_AFTER_PHP_ARTISAN', 'queue:work --timeout=0'),

    /*
    |--------------------------------------------------------------------------
    | Queue Logging
    |--------------------------------------------------------------------------
    |
    | When enabled, Queuefy will log important events like queue worker starts,
    | stops, and errors to your Laravel log.
    |
    */
    'QUEUE_LOG_QUE_COMMAND_FIRED' => env('QUEUE_LOG_QUE_COMMAND_FIRED', false),

    /*
    |--------------------------------------------------------------------------
    | Stop Queue Processing
    |--------------------------------------------------------------------------
    |
    | When set to true, this will prevent the queue worker from being started.
    | Useful for maintenance or when you need to temporarily stop queue processing.
    |
    */
    'STOP_QUEUE' => env('STOP_QUEUE', false),
];
