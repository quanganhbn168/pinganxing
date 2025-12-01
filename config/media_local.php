<?php

return [
    'disk'           => 'public',                // dùng Storage::disk('public')
    'originals_root' => 'userfiles/images',      // nơi chứa ảnh nguyên bản (CKFinder)
    'output_root'    => 'products',              // thư mục gốc để lưu ảnh đã xử lý (có thể đổi theo module)

    // Thông số xử lý mặc định
    'display' => ['w' => 1200, 'h' => 1200, 'fit' => 'contain', 'bg' => '#ffffff', 'quality' => 82],
    'thumb'   => ['w' => 300,  'h' => 300,  'fit' => 'cover',   'quality' => 82],
    'medium'  => ['w' => 600,  'h' => 600,  'fit' => 'cover',   'quality' => 82],

    'watermark' => [
        'enable'   => false,
        'path'     => 'watermarks/logo.png',    // relative to disk
        'position' => 'bottom-right',           // top-left, top-right, bottom-left, bottom-right, center
        'offset'   => 16,
    ],
];
