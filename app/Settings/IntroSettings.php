<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class IntroSettings extends Settings
{
    // ── Trang Về chúng tôi ──────────────────────────────────────
    public ?string $page_title;
    public ?string $page_subtitle;
    public ?int    $page_banner_id;

    // ── Câu chuyện công ty ──────────────────────────────────────
    public ?string $story_title;
    public ?string $story_description;   // Rich text / HTML
    public ?int    $story_image_id;
    public ?int    $founded_year = null;
    public ?string $video_url = null;
    public ?int    $video_thumbnail_id = null;

    // ── Sứ mệnh & Tầm nhìn ─────────────────────────────────────
    public ?string $mission_title;
    public ?string $mission_description;
    public ?string $vision_title;
    public ?string $vision_description;

    // ── Lịch sử phát triển (Timeline) ───────────────────────────
    // [{year, title, description, image_id}]
    public ?array $timeline;

    // ── Stats / Số liệu nổi bật — Repeater ─────────────────────
    // [{icon, value, label, suffix}]
    public ?array $stats;

    // ── Giá trị cốt lõi — Repeater ─────────────────────────────
    // [{icon, title, description}]
    public ?array $core_values;

    // ── Khối nội dung tùy chỉnh (Builder) ───────────────────────
    // [{type, data}] — types: text_block, image_text_block, video_block
    public ?array $custom_blocks;

    // ── CTA ─────────────────────────────────────────────────────
    public ?string $cta_title;
    public ?string $cta_subtitle;
    public ?string $cta_button_label;

    public static function group(): string
    {
        return 'intro';
    }
}
