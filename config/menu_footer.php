<?php

// config/menu_footer.php



return [

    // Mỗi phần tử trong mảng này là một cột menu trong footer

    [

        'title' => 'Về Cnetpos',

        'items' => [

            ['title' => 'Trang chủ', 'url' => '/'],

            ['title' => 'Giới thiệu chung', 'route' => 'frontend.intro.index'],

            ['title' => 'Dự án đã thực hiện', 'url' => '/du-an'],

            ['title' => 'Liên hệ', 'url' => '/lien-he'],

        ]

    ],

    // [

    //     'title' => 'Hỗ trợ khách hàng',

    //     'items' => [

    //         ['title' => 'Chính sách bảo hành', 'url' => '/chinh-sach-bao-hanh'],

    //         ['title' => 'Tư vấn & Báo giá', 'url' => '/tu-van-bao-gia'],

    //         ['title' => 'Câu hỏi thường gặp', 'url' => '/faq'],

    //         ['title' => 'Quy trình lắp đặt', 'url' => '/quy-trinh-lap-dat'],

    //     ]

    // ],

    // Anh có thể thêm các cột khác ở đây

];