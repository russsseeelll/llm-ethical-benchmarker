<?php

return [

    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),


    'headers' => [
        'HTTP-Referer' => env('APP_URL', 'http://localhost'),
        'X-Title'      => env('OPENROUTER_TITLE', 'Ethical LLM Benchmark'),
    ],

    'timeout' => 60,
];
