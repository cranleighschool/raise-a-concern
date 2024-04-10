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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'github' => [
        'key' => env('GITHUB_API_KEY'),
    ],
    'firefly' => [
        'url' => env('FIREFLY_URL'),
        'selfreflections' => [
            'url' => env('FIREFLY_SELFREFLECTIONS_URL'),
            'app' => env('FIREFLY_SELFREFLECTIONS_APP'),
        ],
    ],
    'isams' => [
        'batch_api_key' => env('ISAMS_BATCH_API_KEY'),
        'batch_api_url' => env('ISAMS_BATCH_API_URL', 'https://isams.cranleigh.org/api/batch/1.0/xml.ashx'),
    ],
];
