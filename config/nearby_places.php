<?php

return [
    'enabled' => (bool) env('NEARBY_PLACES_ENABLED', true),

    'provider' => env('NEARBY_PLACES_PROVIDER', 'google'),

    'cache' => [
        'ttl_days' => (int) env('NEARBY_PLACES_CACHE_TTL_DAYS', 30),
        'stale_ttl_days' => (int) env('NEARBY_PLACES_STALE_TTL_DAYS', 90),
        'refresh_throttle_minutes' => (int) env('NEARBY_PLACES_REFRESH_THROTTLE_MINUTES', 60),
    ],

    'refresh' => [
        'lazy' => (bool) env('NEARBY_PLACES_LAZY_REFRESH', true),
        'max_temples_per_run' => (int) env('NEARBY_PLACES_REFRESH_MAX_TEMPLES_PER_RUN', 50),
    ],

    'cost_guard' => [
        'daily_request_limit' => (int) env('GOOGLE_PLACES_DAILY_REQUEST_LIMIT', 500),
    ],

    'categories' => [
        'restaurant' => [
            'label' => 'ร้านอาหารใกล้เคียง',
            'google_types' => ['restaurant'],
            'radius_meters' => 3500,
            'limit' => 6,
            'min_rating' => 3.8,
            'min_reviews' => 20,
        ],
        'cafe' => [
            'label' => 'คาเฟ่ใกล้เคียง',
            'google_types' => ['cafe'],
            'radius_meters' => 3500,
            'limit' => 6,
            'min_rating' => 3.8,
            'min_reviews' => 20,
        ],
        'hotel' => [
            'label' => 'ที่พักใกล้เคียง',
            'google_types' => ['lodging'],
            'radius_meters' => 5000,
            'limit' => 6,
            'min_rating' => 3.6,
            'min_reviews' => 20,
        ],
        'attraction' => [
            'label' => 'สถานที่เที่ยวใกล้เคียง',
            'google_types' => ['tourist_attraction'],
            'radius_meters' => 5000,
            'limit' => 6,
            'min_rating' => 3.8,
            'min_reviews' => 10,
        ],
    ],
];
