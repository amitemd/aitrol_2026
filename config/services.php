<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'vicky' => [
        'url' => env('VICKY_OLLAMA_URL', 'http://100.106.113.53:11434'),
        'model' => env('VICKY_OLLAMA_MODEL', 'qwen3.8:27b-q4_K_M'),
        'timeout' => env('VICKY_OLLAMA_TIMEOUT', 60),
        'core_url' => env('VICKY_CORE_URL'),
        'core_erp_token' => env('VICKY_CORE_ERP_TOKEN'),
        'core_connection_file' => env('VICKY_CORE_CONNECTION_FILE'),
        'cartera_cobrar' => env('VICKY_CARTERA_COBRAR', false),
        'cartera_pagar' => env('VICKY_CARTERA_PAGAR', false),
        'pacientes' => env('VICKY_PACIENTES', false),
    ],

];
