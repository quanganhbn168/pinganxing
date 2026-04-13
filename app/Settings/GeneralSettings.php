<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $site_name;
    public ?string $logo;
    public ?string $favicon;
    public ?string $banner;
    public ?string $phone;
    public ?string $phone_display;
    public ?string $email;
    public ?string $address;
    public ?string $map;
    public ?string $business_code;
    public ?string $tax_code;
    public ?string $zalo;
    public ?string $messenger;
    public ?string $youtube;
    public ?string $tiktok;
    public ?string $bct_link;

    // Tai do an tap tin
    public ?string $catalog_file;

    // SEO
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?string $meta_image;
    public ?string $head_script;
    public ?string $body_start_script;
    public ?string $body_script;

    // Cau hinh Menu
    public ?int    $header_menu_id;

    // Cau hinh Footer dong
    public ?string $footer_background;
    public ?string $footer_col_2_title;
    public ?int    $footer_col_2_menu_id;
    public ?string $footer_col_3_title;
    public ?int    $footer_col_3_menu_id;
    
    // Giới thiệu trang chủ
    public ?string $intro_title;
    public ?string $intro_description;
    public ?string $intro_image;

    // Mô tả các khối
    public ?string $services_title;
    public ?string $services_description;
    public ?string $fields_title;
    public ?string $fields_description;
    // Cấu hình khối Dự án
    public ?string $projects_title;
    public ?string $projects_description;

    // Cấu hình khối Sản phẩm/Phân hệ
    public ?string $products_title;
    public ?string $products_description;

    // Cấu hình khối Bài viết
    public ?string $posts_title;
    public ?string $posts_description;

    // Video gioi thieu trang chu
    public ?string $video_url;
    public ?string $video_title;
    public ?string $video_file;

    // Counter Stats dang Repeater (mang [{icon, value, label, color}])
    public ?array $counters;

    public static function group(): string
    {
        return 'general';
    }
}
