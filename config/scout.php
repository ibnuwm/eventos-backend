<?php

return [
    'driver' => env('SCOUT_DRIVER', 'meilisearch'),
    'prefix' => env('SCOUT_PREFIX', 'vendoros_'),
    'queue' => env('SCOUT_QUEUE', true),
    'after_commit' => false,

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),
        'key' => env('MEILISEARCH_KEY', null),
        'index-settings' => [
            'vendors' => [
                'filterableAttributes' => ['category', 'area', 'tenant_id'],
                'sortableAttributes' => ['sla_punctuality', 'rating', 'starting_price'],
                'searchableAttributes' => ['name', 'category', 'area', 'pic_name'],
            ],
        ],
    ],
];
