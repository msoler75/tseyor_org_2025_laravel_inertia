<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'daily'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'jobs' => [
            'driver' => 'daily',
            'path' => storage_path('logs/jobs.log'),
            'level' => 'info',
            'days' => 14,
        ],

        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
            'replace_placeholders' => true,
        ],

        'notfound' => [
            'driver' => 'single',
            'path' => storage_path('logs/404.log'),
            'level' => 'info',
        ],

        'validation' => [
            'driver' => 'single',
            'path' => storage_path('logs/validation.log'),
            'level' => 'info',
        ],

        'deploy' => [
            'driver' => 'single',
            'path' => storage_path('logs/deploy.log'),
            'level' => 'info',
        ],

        'notificaciones' => [
            'driver' => 'single',
            'path' => storage_path('logs/notificaciones.log'),
            'level' => 'info',
        ],

        'pwa' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pwa.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'smtp' => [
            'driver' => 'daily',
            'path' => storage_path('logs/smtp.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'envios_error' => [
            'driver' => 'single',
            'path' => storage_path('logs/envios_error.log'),
            'level' => 'error',
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'mailing' => [
            'driver' => 'single',
            'path' => storage_path('logs/mailing.log'),
            'level' => 'info',
        ],

        'boletines' => [
            'driver' => 'single',
            'path' => storage_path('logs/boletines.log'),
            'level' => 'info',
        ],

        'mcp' => [
            'driver' => 'single',
            'path' => storage_path('logs/mcp.log'),
            'level' => 'debug',
        ],

        'share' => [
            'driver' => 'daily',
            'path' => storage_path('logs/share.log'),
            'level' => 'info',
            'days' => 14,
        ],

        'inscripciones' => [
            'driver' => 'single',
            'path' => storage_path('logs/inscripciones-' . date('Y-m') . '.log'),
            'level' => 'info',
        ],

        '500' => [
            'driver' => 'daily',
            'path' => storage_path('logs/500.log'),
            'level' => 'error',
            'days' => 7,
        ],
    ],
];
