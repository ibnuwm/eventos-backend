<?php

return [
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'base_uri' => env('OPENAI_API_BASE', 'https://api.openai.com/v1'),
        'model' => env('AI_DEFAULT_MODEL', 'gpt-4o'),
    ],

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'is_production' => false,
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_API_TOKEN'),
    ],
];
