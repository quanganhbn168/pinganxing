<?php

return [
    // DASHBOARD
    [
        'title' => 'Dashboard',
        'icon' => 'bi bi-speedometer2',
        'route' => 'admin.dashboard',
        'active_pattern' => 'admin.dashboard',
        'permission' => 'view_dashboard',
    ],

    // ================================================================
    // NHÓM 1: QUẢN LÝ CÔNG VIỆC (JOB & CRM)
    // ================================================================
    ['type' => 'header', 'title' => 'QUẢN LÝ CÔNG VIỆC'],

    [
        'title' => 'Khách hàng (CRM)',
        'icon' => 'bi bi-people-fill', 
        'route' => 'admin.customers.index',
        'active_pattern' => 'admin.customers.*',
        'permission' => 'view_customers',
    ],
    [
        'title' => 'Phiếu việc (Job)',
        'icon' => 'bi bi-tools', 
        'active_pattern' => ['admin.work-orders.*', 'admin.my-work-orders.*', 'admin.tag-manager.*'],
        'permission' => 'view_work_orders',
        'submenu' => [
            [
                'title' => 'Danh sách phiếu việc',
                'route' => 'admin.work-orders.index',
                'active_pattern' => 'admin.work-orders.index',
                'permission' => 'view_work_orders',
            ],
            [
                'title' => 'Tạo phiếu việc',     
                'route' => 'admin.work-orders.create',   
                'active_pattern' => 'admin.work-orders.create',
                'permission' => 'create_work_orders',
            ],
            [
                'title' => 'Việc của tôi',       
                'route' => 'admin.my-work-orders.index', 
                'active_pattern' => 'admin.my-work-orders.*',
                // Không cần permission - ai cũng thấy việc của mình
            ],
            [
                'title' => 'Quản lý Tags',       
                'route' => 'admin.tag-manager.index', 
                'active_pattern' => 'admin.tag-manager.*',
                'permission' => 'view_tags',
            ],
        ],
    ],
    [
        'title' => 'Quản lý Vật tư',
        'icon' => 'bi bi-box-seam',
        'permission' => 'view_materials',
        'active_pattern' => ['admin.materials.*', 'admin.returned-materials.*', 'admin.suppliers.*'],
        'submenu' => [
            [
                'title' => 'Kho vật tư',
                'route' => 'admin.materials.index',
                'active_pattern' => 'admin.materials.*',
                'permission' => 'view_materials',
            ],
            [
                'title' => 'Vật tư thu hồi',
                'route' => 'admin.returned-materials.index',
                'active_pattern' => 'admin.returned-materials.*',
                'permission' => 'view_materials',
            ],
            [
                'title' => 'Nhà cung cấp',
                'route' => 'admin.suppliers.index',
                'active_pattern' => 'admin.suppliers.*',
                'permission' => 'view_materials',
            ],
        ],
    ],
    [
        'title' => 'Bảo hành',
        'icon' => 'bi bi-shield-check', 
        'route' => 'admin.warranty.index',
        'active_pattern' => 'admin.warranty.*',
        'permission' => 'view_warranty',
    ],
    [
        'title' => 'Tài chính',
        'icon' => 'bi bi-currency-dollar', 
        'route' => 'admin.finance.index',
        'active_pattern' => 'admin.finance.*',
        'permission' => 'view_finance',
    ],

    // ================================================================
    // NHÓM 2: SẢN PHẨM & DỊCH VỤ
    // ================================================================
    ['type' => 'header', 'title' => 'SẢN PHẨM & DỊCH VỤ'],

    [
        'title' => 'Quản lý sản phẩm',
        'icon' => 'bi bi-box-seam',
        'permission' => 'view_products',
        'active_pattern' => ['admin.products.*', 'admin.categories.*', 'admin.attributes.*'],
        'submenu' => [
            ['title' => 'Danh mục sản phẩm', 'route' => 'admin.categories.index', 'active_pattern' => 'admin.categories.*', 'permission' => 'view_categories'],
            ['title' => 'Sản phẩm',          'route' => 'admin.products.index',   'active_pattern' => 'admin.products.*', 'permission' => 'view_products'],
            ['title' => 'Thuộc tính',        'route' => 'admin.attributes.index', 'active_pattern' => 'admin.attributes.*', 'permission' => 'view_products'],
        ],
    ],

    // ================================================================
    // NHÓM 3: NỘI DUNG & MEDIA
    // ================================================================
    ['type' => 'header', 'title' => 'NỘI DUNG & MEDIA'],

    [
        'title' => 'Quản lý Media',
        'icon' => 'bi bi-folder2-open', 
        'route' => 'admin.media.index', 
        'active_pattern' => 'admin.media.*',
        'permission' => 'view_media',
    ],
    [
        'title' => 'Quản lý menu',
        'icon' => 'fas fa-bars', 
        'route' => 'admin.menus.index', 
        'active_pattern' => 'admin.menus.*',
        'permission' => 'view_settings', // Menu thuộc settings
    ],
    [
        'title' => 'Quản lý bài viết',
        'icon' => 'bi bi-pencil-square',
        'permission' => 'view_posts',
        'active_pattern' => ['admin.post-categories.*', 'admin.posts.*'],
        'submenu' => [
            ['title' => 'Danh mục bài viết', 'route' => 'admin.post-categories.index', 'active_pattern' => 'admin.post-categories.*', 'permission' => 'view_posts'],
            ['title' => 'Bài viết',          'route' => 'admin.posts.index',           'active_pattern' => 'admin.posts.*', 'permission' => 'view_posts'],
        ],
    ],
    [
        'title' => 'Thư viện & Hiển thị',
        'icon' => 'bi bi-collection',
        'permission' => 'view_slides',
        'active_pattern' => ['admin.slides.*', 'admin.testimonials.*', 'admin.intros.*', 'admin.brands.*'],
        'submenu' => [
            ['title' => 'Slide trang chủ',   'route' => 'admin.slides.index',       'active_pattern' => 'admin.slides.*', 'permission' => 'view_slides'],
            ['title' => 'Feedback',          'route' => 'admin.testimonials.index', 'active_pattern' => 'admin.testimonials.*', 'permission' => 'view_slides'],
            ['title' => 'Giới thiệu',        'route' => 'admin.intros.index',       'active_pattern' => 'admin.intros.*', 'permission' => 'view_pages'],
            ['title' => 'Thương hiệu',       'route' => 'admin.brands.index',       'active_pattern' => 'admin.brands.*', 'permission' => 'view_slides'],
        ],
    ],

    // ================================================================
    // NHÓM 4: DỰ ÁN - ĐỐI TÁC - TUYỂN DỤNG
    // ================================================================
    ['type' => 'header', 'title' => 'MỞ RỘNG'],

    [
        'title' => 'Quản lý Dự án',
        'icon' => 'bi bi-building',
        'permission' => 'view_projects',
        'active_pattern' => ['admin.project-categories.*', 'admin.projects.*'],
        'submenu' => [
            ['title' => 'Danh mục Dự án', 'route' => 'admin.project-categories.index', 'active_pattern' => 'admin.project-categories.*', 'permission' => 'view_projects'],
            ['title' => 'Dự án',          'route' => 'admin.projects.index',           'active_pattern' => 'admin.projects.*', 'permission' => 'view_projects'],
        ],
    ],
    [
        'title' => 'Quản lý Lĩnh vực',
        'icon' => 'bi bi-diagram-3',
        'permission' => 'view_projects', // Dùng chung với projects
        'active_pattern' => ['admin.field-categories.*', 'admin.fields.*'],
        'submenu' => [
            ['title' => 'Danh mục Lĩnh vực', 'route' => 'admin.field-categories.index', 'active_pattern' => 'admin.field-categories.*', 'permission' => 'view_projects'],
            ['title' => 'Lĩnh vực',          'route' => 'admin.fields.index',           'active_pattern' => 'admin.fields.*', 'permission' => 'view_projects'],
        ],
    ],
    [
        'title' => 'Quản lý Đại lý',
        'icon' => 'bi bi-shop-window',
        'route' => 'admin.agents.index',
        'active_pattern' => 'admin.agents.*',
        'permission' => 'view_agents',
    ],
    [
        'title' => 'Quản lý Tuyển dụng',
        'icon' => 'bi bi-briefcase',
        'permission' => 'view_careers',
        'active_pattern' => ['admin.careers.*', 'admin.career_applications.*'],
        'submenu' => [
            ['title' => 'Tin tuyển dụng',    'route' => 'admin.careers.index',             'active_pattern' => 'admin.careers.*', 'permission' => 'view_careers'],
            ['title' => 'Hồ sơ ứng tuyển',   'route' => 'admin.career-applications.index', 'active_pattern' => 'admin.career-applications.*', 'permission' => 'view_careers'],
        ],
    ],

    // ================================================================
    // NHÓM 5: HỆ THỐNG
    // ================================================================
    ['type' => 'header', 'title' => 'HỆ THỐNG'],

    [
        'title' => 'Quản lý Nhân viên',
        'icon' => 'bi bi-person-badge-fill',
        'permission' => 'view_staff',
        'active_pattern' => ['admin.staff.*'],
        'submenu' => [
            ['title' => 'Danh sách nhân viên', 'route' => 'admin.staff.index',  'active_pattern' => 'admin.staff.index', 'permission' => 'view_staff'],
            ['title' => 'Thêm nhân viên',      'route' => 'admin.staff.create', 'active_pattern' => 'admin.staff.create', 'permission' => 'create_staff'],
        ],
    ],
    
    [
        'title' => 'Quản lý User Web',
        'icon' => 'bi bi-person-gear',
        'permission' => 'view_staff', // User web = staff
        'active_pattern' => 'admin.users.*',
        'submenu' => [
            ['title' => 'Danh sách người dùng', 'route' => 'admin.users.index',  'active_pattern' => 'admin.users.*', 'permission' => 'view_staff'],
            ['title' => 'Thêm người dùng',      'route' => 'admin.users.create', 'active_pattern' => 'admin.users.create', 'permission' => 'create_staff'],
        ],
    ],
    
    [
        'title' => 'Phân quyền',
        'icon' => 'bi bi-shield-check',
        'permission' => 'view_roles',
        'active_pattern' => 'admin.roles.*',
        'submenu' => [
            ['title' => 'Vai trò & Quyền', 'route' => 'admin.roles.index', 'active_pattern' => 'admin.roles.*', 'permission' => 'view_roles'],
        ],
    ],
    [
        'title' => 'Page Settings',
        'icon' => 'bi bi-file-earmark-richtext',
        'route' => 'admin.pages.index',
        'active_pattern' => 'admin.pages.*',
        'permission' => 'view_pages',
    ],
    [
        'title' => 'Cấu hình chung',
        'icon' => 'bi bi-gear',
        'route' => 'admin.settings.index',
        'active_pattern' => 'admin.settings.*',
        'permission' => 'view_settings',
    ],
];