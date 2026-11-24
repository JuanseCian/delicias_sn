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

    'cake_ai' => [
        'provider' => env('CAKE_AI_PROVIDER'),
        'endpoint' => env('CAKE_AI_ENDPOINT', 'https://api.openai.com/v1/chat/completions'),
        'key' => env('CAKE_AI_API_KEY'),
        'model' => env('CAKE_AI_MODEL', 'gpt-4o-mini'),
    ],

    'whatsapp_cloud' => [
        'api_url' => env('WHATSAPP_CLOUD_API_URL', 'https://graph.facebook.com'),
        'api_version' => env('WHATSAPP_CLOUD_API_VERSION', 'v22.0'),
        'phone_number_id' => env('WHATSAPP_CLOUD_PHONE_NUMBER_ID'),
        'access_token' => env('WHATSAPP_CLOUD_ACCESS_TOKEN'),
        'review_template' => env('WHATSAPP_CLOUD_REVIEW_TEMPLATE', 'delicias_resena_entregada'),
        'template_language' => env('WHATSAPP_CLOUD_TEMPLATE_LANGUAGE', 'es_AR'),
    ],

];
