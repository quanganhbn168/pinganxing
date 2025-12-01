<?php
return [
    [
        'title' => 'Trang Chủ',
        'url' => '/',
    ],
    [
        'title' => 'Về Chúng Tôi',
        'route' => 'frontend.intro.index',
        'children' => [],
        'dynamic_children' => [
            'model' => \App\Models\Intro::class,
            'method' => 'getSubMenuItems',
            'route_name' => 'frontend.slug.handle',
        ]
    ],
    [
        'title' => 'Lĩnh vực',
        'route' => 'frontend.fields.index',
        'children' => [],
        'dynamic_children' => [
            'model' => \App\Models\FieldCategories::class,
            'parent_id' => 0,
            'route_name' => 'frontend.slug.handle',
        ]
    ],
    [
        'title' => 'Dự án',
        'route' => 'frontend.projects.index',
        'children' => [],
        'dynamic_children' => [
            'model' => \App\Models\ProjectCategories::class,
            'parent_id' => 0,
            'route_name' => 'frontend.slug.handle',
        ]
    ],
    
    [
        'title' => 'Sản Phẩm',
        'url' => '/san-pham',
        'children' => [],
        'dynamic_children' => [ 
            'model' => \App\Models\Category::class,
            'parent_id' => 0, 
            'route_name' => 'frontend.slug.handle', 
        ]
    ],
    [
        'title' => 'Tin Tức',
        'url' => '/tin-tuc',
        'children' => [],
        'dynamic_children' => [
            'model' => \App\Models\PostCategory::class,
            'parent_id' => 0, 
            'route_name' => 'frontend.slug.handle', 
        ]
    ],
    [
        'title' => 'Liên Hệ & Tư Vấn',
        'url' => '/lien-he',
    ],
];