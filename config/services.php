<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Terceros Servicios
    |--------------------------------------------------------------------------
    |
    | Este archivo es para almacenar las credenciales de servicios de terceros
    | como Mailgun, Postmark, AWS y más. Este archivo proporciona el lugar
    | de facto para este tipo de información, lo que permite a los paquetes tener
    | un archivo convencional para localizar las diversas credenciales de servicio.
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
        'apikey' => env('GOOGLE_MAPS_API_KEY')
    ],


    'word_to_markdown' => [
        'url' => env('WORD_TO_MD_URL')
    ],

    'audio_converter' => [
        'url' => env('AUDIO_CONVERTER_URL'),
        'frecuencia' => env('AUDIO_CONVERTER_FRECUENCIA', 22050),
        'kbps' => env('AUDIO_CONVERTER_KBPS', '24k')
    ],

    'openai_key' => env('OPEN_AI_KEY', NULL),

];
