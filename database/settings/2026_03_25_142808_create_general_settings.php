<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // ── Thương hiệu ──────────────────────────────────────────
        $this->migrator->add('general.site_name', 'Ping An Xing');
        $this->migrator->add('general.company_name', 'Ping An Xing');
        $this->migrator->add('general.description', 'Ping An Xing - Dịch vụ du lịch, visa, thuê xe và giải pháp đồng hành trọn gói.');
        $this->migrator->add('general.logo', '');
        $this->migrator->add('general.favicon', '');
        $this->migrator->add('general.banner', '');
        $this->migrator->add('general.catalog_file', '');
        $this->migrator->add('general.business_code', null);
        $this->migrator->add('general.tax_code', null);

        // ── Liên hệ ──────────────────────────────────────────────
        $this->migrator->add('general.phone', null);
        $this->migrator->add('general.phone_display', null);
        $this->migrator->add('general.email', null);
        $this->migrator->add('general.address', null);
        $this->migrator->add('general.map', '');
        $this->migrator->add('general.working_hours', null);

        // ── Mạng xã hội ─────────────────────────────────────────
        $this->migrator->add('general.zalo', null);
        $this->migrator->add('general.messenger', null);
        $this->migrator->add('general.youtube', null);
        $this->migrator->add('general.tiktok', null);
        $this->migrator->add('general.bct_link', '');

        // ── SEO & Scripts ────────────────────────────────────────
        $this->migrator->add('general.meta_description', 'Ping An Xing - dịch vụ du lịch và đồng hành trọn gói');
        $this->migrator->add('general.meta_keywords', 'du lịch, tour, vé máy bay, khách sạn, visa, thuê xe, Ping An Xing');
        $this->migrator->add('general.meta_image', null);
        $this->migrator->add('general.head_script', '');
        $this->migrator->add('general.body_start_script', null);
        $this->migrator->add('general.body_script', '');

        // ── Cấu hình Menu ────────────────────────────────────────
        $this->migrator->add('general.header_menu_id', null);

        // ── Cấu hình Footer ─────────────────────────────────────
        $this->migrator->add('general.footer_background', '');
        $this->migrator->add('general.footer_col_2_title', 'Về Ping An Xing');
        $this->migrator->add('general.footer_col_2_menu_id', null);
        $this->migrator->add('general.footer_col_3_title', 'Chính sách & Hướng dẫn');
        $this->migrator->add('general.footer_col_3_menu_id', null);
    }
};
