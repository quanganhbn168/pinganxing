<?php

return [
    // DASHBOARD
    [
        'title' => 'Dashboard',
        'icon' => 'bi bi-speedometer2',
        'route' => 'admin.dashboard',
        'permission' => 'view-dashboard',
    ],

    // ===== NHÓM 0: MEDIA (QUẢN LÝ TẬP TRUNG) =====
    ['type' => 'header', 'title' => 'TÀI NGUYÊN & MEDIA'],
    [
        'title' => 'Quản lý Media',
        'icon' => 'bi bi-folder2-open', // Icon folder mở
        'route' => 'admin.media.index', // Route mới cho Media Manager
        'active_pattern' => 'admin.media.*',
        'permission' => 'manage-media',
    ],
    [
        'title' => 'Quản lý menu',
        'icon' => 'fas fa-bars', // Icon folder mở
        'route' => 'admin.menus.index', // Route mới cho Media Manager
        'active_pattern' => 'admin.menus.*',
        'permission' => 'manage-menu',
    ],
    
    // ===== NHÓM 1: SẢN PHẨM & DỊCH VỤ =====
    ['type' => 'header', 'title' => 'SẢN PHẨM'],

    [
        'title' => 'Quản lý sản phẩm',
        'icon' => 'bi bi-box-seam',
        'permission' => 'manage-products',
        'active_pattern' => ['admin.products.*', 'admin.categories.*', 'admin.attributes.*'],
        'submenu' => [
            ['title' => 'Danh mục sản phẩm', 'route' => 'admin.categories.index', 'active_pattern' => 'admin.categories.*'],
            ['title' => 'Sản phẩm',            'route' => 'admin.products.index',  'active_pattern' => 'admin.products.*'],
            ['title' => 'Thuộc tính',         'route' => 'admin.attributes.index','active_pattern' => 'admin.attributes.*'],
        ],
    ],

    // ===== NHÓM 2: NỘI DUNG WEBSITE =====
    ['type' => 'header', 'title' => 'NỘI DUNG WEBSITE'],

    [
        'title' => 'Quản lý bài viết',
        'icon' => 'bi bi-pencil-square',
        'permission' => 'manage-posts',
        'active_pattern' => ['admin.post-categories.*', 'admin.posts.*'],
        'submenu' => [
            ['title' => 'Danh mục bài viết', 'route' => 'admin.post-categories.index', 'active_pattern' => 'admin.post-categories.*'],
            ['title' => 'Bài viết',          'route' => 'admin.posts.index',            'active_pattern' => 'admin.posts.*'],
        ],
    ],
    [
        'title' => 'Thư viện & Hiển thị',
        'icon' => 'bi bi-collection',
        'active_pattern' => ['admin.slides.*', 'admin.testimonials.*', 'admin.intros.*', 'admin.brands.*'],
        'submenu' => [
            ['title' => 'Slide trang chủ',  'route' => 'admin.slides.index',        'active_pattern' => 'admin.slides.*'],
            ['title' => 'Feedback (Testimonial)', 'route' => 'admin.testimonials.index', 'active_pattern' => 'admin.testimonials.*'],
            ['title' => 'Giới thiệu (Pages)',      'route' => 'admin.intros.index',       'active_pattern' => 'admin.intros.*'],
            ['title' => 'Thương hiệu (Brand)',     'route' => 'admin.brands.index',       'active_pattern' => 'admin.brands.*'],
        ],
    ],

    // ===== NHÓM 3: DỰ ÁN & LĨNH VỰC =====
    ['type' => 'header', 'title' => 'DỰ ÁN & LĨNH VỰC'],

    [
        'title' => 'Quản lý Dự án',
        'icon' => 'bi bi-building', // Đổi icon building cho hợp dự án
        'active_pattern' => ['admin.project-categories.*', 'admin.projects.*'],
        'submenu' => [
            ['title' => 'Danh mục Dự án', 'route' => 'admin.project-categories.index', 'active_pattern' => 'admin.project-categories.*'],
            ['title' => 'Dự án',          'route' => 'admin.projects.index',            'active_pattern' => 'admin.projects.*'],
        ],
    ],
    [
        'title' => 'Quản lý Lĩnh vực',
        'icon' => 'bi bi-diagram-3', // Đổi icon diagram cho hợp lĩnh vực hoạt động
        'permission' => 'manage-fields',
        'active_pattern' => ['admin.field-categories.*', 'admin.fields.*'],
        'submenu' => [
            ['title' => 'Danh mục Lĩnh vực', 'route' => 'admin.field-categories.index', 'active_pattern' => 'admin.field-categories.*'],
            ['title' => 'Lĩnh vực',          'route' => 'admin.fields.index',            'active_pattern' => 'admin.fields.*'],
        ],
    ],

    // ===== NHÓM 4: ĐỐI TÁC & TUYỂN DỤNG =====
    ['type' => 'header', 'title' => 'ĐỐI TÁC & TUYỂN DỤNG'],

    [
        'title' => 'Quản lý Đại lý',
        'icon' => 'bi bi-shop-window', // Icon cửa hàng/đại lý
        'route' => 'admin.agents.index',
        'active_pattern' => 'admin.agents.*',
        'permission' => 'manage-agents',
    ],
    [
        'title' => 'Quản lý Tuyển dụng',
        'icon' => 'bi bi-briefcase',
        'permission' => 'manage-careers',
        // Active pattern cho cả tin tuyển dụng và hồ sơ ứng tuyển
        'active_pattern' => ['admin.careers.*', 'admin.career_applications.*'],
        'submenu' => [
            // Quản lý bài đăng tuyển dụng
            ['title' => 'Tin tuyển dụng',      'route' => 'admin.careers.index',             'active_pattern' => 'admin.careers.*'],
            
            // Quản lý CV/Form ứng tuyển gửi về
            [
                'title' => 'Hồ sơ ứng tuyển',
                'route' => 'admin.career-applications.index',
                'active_pattern' => 'admin.career-applications.*'
            ],
        ],
    ],
    // ===== NHÓM MỚI: QUẢN LÝ CÔNG VIỆC (JOB) =====
    ['type' => 'header', 'title' => 'QUẢN LÝ CÔNG VIỆC'],

    [
        'title' => 'Phiếu việc (Job)',
        'icon' => 'bi bi-tools', // Icon công cụ
        // Pattern này để giữ menu mở khi đang ở trang tạo hoặc xem việc của tôi
        'active_pattern' => ['admin.work-orders.*', 'admin.my-work-orders.*'],
        'submenu' => [
            [
                'title' => 'Danh sách phiếu việc',
                'route' => 'admin.work-orders.index',
                'active_pattern' => 'admin.work-orders.index'
            ],
            [
                'title' => 'Tạo phiếu việc',     
                'route' => 'admin.work-orders.create',   
                'active_pattern' => 'admin.work-orders.create'
            ],
            // Dành cho Nhân viên xem việc
            [
                'title' => 'Việc của tôi',       
                'route' => 'admin.my-work-orders.index', 
                'active_pattern' => 'admin.my-work-orders.*' // Bao gồm cả trang chi tiết
            ],
        ],
    ],
    [
        'title' => 'Duyệt & Doanh thu',
        'icon' => 'bi bi-cash-stack', // Icon tiền
        'route' => 'admin.task-audit.index',
        'active_pattern' => 'admin.task-audit.*',
        // 'permission' => 'audit-tasks', // Sau này có thể thêm quyền vào đây
    ],
    // ===== NHÓM 5: HỆ THỐNG =====
    ['type' => 'header', 'title' => 'HỆ THỐNG'],

    [
        'title' => 'Quản lý người dùng',
        'icon' => 'bi bi-person-gear',
        'permission' => 'manage-users',
        'active_pattern' => 'admin.users.*',
        'submenu' => [
            ['title' => 'Danh sách người dùng', 'route' => 'admin.users.index',  'active_pattern' => 'admin.users.*'],
            ['title' => 'Thêm người dùng',       'route' => 'admin.users.create', 'active_pattern' => 'admin.users.create'],
        ],
    ],
    [
        'title' => 'Page Settings',
        'icon' => 'bi bi-file-earmark-richtext',
        'route' => 'admin.pages.index',
        'active_pattern' => 'admin.pages.*',
        'permission' => 'manage-pages',
    ],
    [
        'title' => 'Phân quyền',
        'icon' => 'bi bi-shield-check',
        'permission' => 'manage-roles',
        'active_pattern' => 'admin.roles.*',
        'submenu' => [
            ['title' => 'Vai trò & Quyền', 'route' => 'admin.roles.index', 'active_pattern' => 'admin.roles.*'],
        ],
    ],
    [
        'title' => 'Cấu hình chung',
        'icon' => 'bi bi-gear',
        'route' => 'admin.settings.index',
        'active_pattern' => 'admin.settings.*',
        'permission' => 'manage-settings',
    ],
];