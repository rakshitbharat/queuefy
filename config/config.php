<?php

/*
 * You can place your custom package configuration in here.
 */
return [
#    'QUEUE_COMMAND_FULL_PATH' => env('QUEUE_COMMAND', 'php artisan queue:work --tries=3'),
    'QUEUE_COMMAND_AFTER_PHP_ARTISAN' => env('QUEUE_COMMAND_AFTER_PHP_ARTISAN', 'queue:work --tries=3')
];
