<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // ── Khối Giới thiệu ──────────────────────────────────────
        $this->migrator->add('home.intro_title', null);
        $this->migrator->add('home.intro_description', null);
        $this->migrator->add('home.intro_image', null);

        // ── Điểm nổi bật giới thiệu ─────────────────────────────
        $this->migrator->add('home.intro_features', []);

        // ── Video giới thiệu ─────────────────────────────────────
        $this->migrator->add('home.video_url', null);
        $this->migrator->add('home.video_title', null);
        $this->migrator->add('home.video_file', null);

        // ── Bộ đếm thống kê ─────────────────────────────────────
        $this->migrator->add('home.counters', []);

        // ── Tiêu đề & Mô tả các khối ────────────────────────────
        $this->migrator->add('home.services_title', 'Dịch Vụ Cung Cấp');
        $this->migrator->add('home.services_description', null);
        $this->migrator->add('home.fields_title', 'Lĩnh Vực Hoạt Động');
        $this->migrator->add('home.fields_description', null);
        $this->migrator->add('home.projects_title', 'Dự Án Tiêu Biểu');
        $this->migrator->add('home.projects_description', null);
        $this->migrator->add('home.products_title', 'Những hành trình được yêu thích nhất');
        $this->migrator->add('home.products_description', null);
        $this->migrator->add('home.posts_title', 'Tin Tức Mới Nhất');
        $this->migrator->add('home.posts_description', null);
    }
};
