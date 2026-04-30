<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PageSettings extends Settings
{
    // Trang Sản phẩm
    public ?string $products_title;
    public ?string $products_headline;
    public ?string $products_description;
    public ?string $products_content;
    public ?string $products_banner;
    public ?string $products_leaderboard_subline;
    public ?string $products_leaderboard_description;
    public ?array $products_leaderboard_actions;
    public ?array $products_leaderboard_stats;
    public ?string $products_cta_title;
    public ?string $products_cta_description;
    public ?string $products_cta_link;

    // Trang Dự án
    public ?string $projects_title;
    public ?string $projects_headline;
    public ?string $projects_description;
    public ?string $projects_content;
    public ?string $projects_banner;
    public ?string $projects_leaderboard_subline;
    public ?string $projects_leaderboard_description;
    public ?array $projects_leaderboard_actions;
    public ?array $projects_leaderboard_stats;
    public ?string $projects_cta_title;
    public ?string $projects_cta_description;
    public ?string $projects_cta_link;

    // Trang Dịch vụ
    public ?string $services_title;
    public ?string $services_headline;
    public ?string $services_description;
    public ?string $services_content;
    public ?string $services_banner;
    public ?string $services_leaderboard_subline;
    public ?string $services_leaderboard_description;
    public ?array $services_leaderboard_actions;
    public ?array $services_leaderboard_stats;
    public ?string $services_cta_title;
    public ?string $services_cta_description;
    public ?string $services_cta_link;

    // Trang Lĩnh vực
    public ?string $fields_title;
    public ?string $fields_headline;
    public ?string $fields_description;
    public ?string $fields_content;
    public ?string $fields_banner;
    public ?string $fields_leaderboard_subline;
    public ?string $fields_leaderboard_description;
    public ?array $fields_leaderboard_actions;
    public ?array $fields_leaderboard_stats;
    public ?string $fields_cta_title;
    public ?string $fields_cta_description;
    public ?string $fields_cta_link;

    // Trang Tin tức
    public ?string $posts_title;
    public ?string $posts_headline;
    public ?string $posts_description;
    public ?string $posts_content;
    public ?string $posts_banner;
    public ?string $posts_leaderboard_subline;
    public ?string $posts_leaderboard_description;
    public ?array $posts_leaderboard_actions;
    public ?array $posts_leaderboard_stats;
    public ?string $posts_cta_title;
    public ?string $posts_cta_description;
    public ?string $posts_cta_link;

    // Trang Tuyển dụng
    public ?string $careers_title;
    public ?string $careers_headline;
    public ?string $careers_description;
    public ?string $careers_content;
    public ?string $careers_banner;
    public ?string $careers_leaderboard_subline;
    public ?string $careers_leaderboard_description;
    public ?array $careers_leaderboard_actions;
    public ?array $careers_leaderboard_stats;
    public ?string $careers_cta_title;
    public ?string $careers_cta_description;
    public ?string $careers_cta_link;

    // Trang Liên hệ
    public ?string $contact_title;
    public ?string $contact_headline;
    public ?string $contact_description;
    public ?string $contact_content;
    public ?string $contact_banner;
    public ?string $contact_leaderboard_subline;
    public ?string $contact_leaderboard_description;
    public ?array $contact_leaderboard_actions;
    public ?array $contact_leaderboard_stats;
    public ?string $contact_cta_title;
    public ?string $contact_cta_description;
    public ?string $contact_cta_link;
    // Trang Đại lý
    public ?string $agency_title;
    public ?string $agency_headline;
    public ?string $agency_description;
    public ?string $agency_content;
    public ?string $agency_banner;
    public ?string $agency_leaderboard_subline;
    public ?string $agency_leaderboard_description;
    public ?array $agency_leaderboard_actions;
    public ?array $agency_leaderboard_stats;
    public ?string $agency_cta_title;
    public ?string $agency_cta_description;
    public ?string $agency_cta_link;

    // Trang Tư vấn triển khai
    public ?string $consulting_title;
    public ?string $consulting_headline;
    public ?string $consulting_description;
    public ?string $consulting_content;
    public ?string $consulting_banner;
    public ?string $consulting_leaderboard_subline;
    public ?string $consulting_leaderboard_description;
    public ?array $consulting_leaderboard_actions;
    public ?array $consulting_leaderboard_stats;
    public ?string $consulting_cta_title;
    public ?string $consulting_cta_description;
    public ?string $consulting_cta_link;

    public static function group(): string
    {
        return 'page';
    }
}
