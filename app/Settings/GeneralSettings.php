<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // ── Thương hiệu ──────────────────────────────────────────────
    public ?string $site_name;
    public ?string $company_name;
    public ?string $description;
    public ?string $logo;
    public ?string $favicon;
    public ?string $banner;
    public ?string $catalog_file;
    public ?string $business_code;
    public ?string $tax_code;

    // ── Liên hệ ──────────────────────────────────────────────────
    public ?string $phone;
    public ?string $phone_display;
    public ?string $email;
    public ?string $address;
    public ?string $map;
    public ?string $working_hours;

    // ── Mạng xã hội ─────────────────────────────────────────────
    public ?string $facebook;
    public ?string $zalo;
    public ?string $messenger;
    public ?string $whatsapp;
    public ?string $wechat;
    public ?string $wechat_qr;
    public ?string $youtube;
    public ?string $tiktok;
    public ?string $bct_link;

    // ── Cấu hình Menu ────────────────────────────────────────────
    public ?int    $header_menu_id;

    // ── Cấu hình Footer ─────────────────────────────────────────
    public ?string $footer_background;
    public ?string $footer_col_2_title;
    public ?int    $footer_col_2_menu_id;
    public ?string $footer_col_3_title;
    public ?int    $footer_col_3_menu_id;

    // ── SEO ──────────────────────────────────────────────────────
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?string $meta_image;
    public ?string $head_script;
    public ?string $body_start_script;
    public ?string $body_script;

    public static function group(): string
    {
        return 'general';
    }
}
