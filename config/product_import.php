<?php

return [
    'source_disk' => 'local',
    'asset_disk' => 'public',
    'source_directory' => 'product-imports/sources',
    'asset_directory' => 'product-imports/assets',

    'queue' => [
        'connection' => env('PRODUCT_IMPORT_QUEUE_CONNECTION', 'redis_product_import'),
        'name' => env('PRODUCT_IMPORT_QUEUE', 'product-import'),
        'status_ttl' => env('PRODUCT_IMPORT_STATUS_TTL', 3600),
    ],

    'default_column_map' => [
        'default' => [
            'start_row' => 1,
            'code' => null,
            'name' => null,
            'description' => null,
            'content' => null,
            'specifications' => null,
            'price' => null,
            'category_path' => null,
        ],
        'sheets' => [],
    ],

    'extract' => [
        'max_columns' => 80,
        'ignore_images_before_row' => 2,
        'ignore_images_before_column' => 1,
        'min_image_bytes' => 1024,
    ],

    'commit' => [
        'default_stock' => 0,
        'default_status' => true,
        'default_product_type' => 'physical',
        'update_existing_by_code' => true,
    ],
];
