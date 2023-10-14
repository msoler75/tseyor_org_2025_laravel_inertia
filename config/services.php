<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google_maps' => [
        'apikey'=>env('GOOGLE_MAPS_API_KEY')
    ],


    'word_to_markdown' => [
        'url' => env('WORD_TO_MD_URL')
    ],

    'audio_converter' => [
        'url' => env('AUDIO_CONVERTER_URL'),
        'frecuencia'=> env('AUDIO_CONVERTER_FRECUENCIA', 22050),
        'kbps' => env('AUDIO_CONVERTER_KBPS', '24k')
    ]

];
