<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Thông tin cơ bản
        $this->migrator->add('general.site_name', 'CNETPos CMS');
        $this->migrator->add('general.logo', '');
        $this->migrator->add('general.favicon', '');
        $this->migrator->add('general.banner', '');
        $this->migrator->add('general.phone', '0900.000.000');
        $this->migrator->add('general.phone_display', null);
        $this->migrator->add('general.email', 'contact@cnetpos.com');
        $this->migrator->add('general.address', '123 ABC Street, Hanoi');
        $this->migrator->add('general.map', '');
        $this->migrator->add('general.business_code', null);
        $this->migrator->add('general.tax_code', null);

        // Mạng xã hội & Khách hàng
        $this->migrator->add('general.zalo', 'https://zalo.me/');
        $this->migrator->add('general.messenger', 'https://m.me/');
        $this->migrator->add('general.youtube', 'https://youtube.com/');
        $this->migrator->add('general.tiktok', 'https://tiktok.com/');

        // Tài liệu & Pháp lý
        $this->migrator->add('general.bct_link', '');
        $this->migrator->add('general.catalog_file', '');

        // SEO & Scripts
        $this->migrator->add('general.meta_description', 'CNETPos platform for enterprise');
        $this->migrator->add('general.meta_keywords', 'cms, corporate');
        $this->migrator->add('general.meta_image', null);
        $this->migrator->add('general.head_script', '');
        $this->migrator->add('general.body_start_script', null);
        $this->migrator->add('general.body_script', '');
        
        // Counter (Repeater)
        $this->migrator->add('general.counters', [
            ['icon' => 'clock', 'value' => '10', 'label' => 'Năm kinh nghiệm', 'color' => 'blue'],
            ['icon' => 'check-circle', 'value' => '500', 'label' => 'Dự án triển khai', 'color' => 'emerald'],
            ['icon' => 'users', 'value' => '50', 'label' => 'Chuyên gia, kỹ sư', 'color' => 'amber'],
            ['icon' => 'briefcase', 'value' => '1500', 'label' => 'Khách hàng đồng hành', 'color' => 'violet'],
        ]);

        // Video
        $this->migrator->add('general.video_url', '');
        $this->migrator->add('general.video_file', null);
        $this->migrator->add('general.video_title', 'Video giới thiệu');

        // Intro
        $this->migrator->add('general.intro_title', 'Giới thiệu công ty');
        $this->migrator->add('general.intro_description', '');
        $this->migrator->add('general.intro_image', '');

        // Menu
        $this->migrator->add('general.header_menu_id', null);

        // Footer Dynamic
        $this->migrator->add('general.footer_background', '');
        $this->migrator->add('general.footer_col_2_title', 'Về Cnetpos');
        $this->migrator->add('general.footer_col_2_menu_id', null);
        $this->migrator->add('general.footer_col_3_title', 'Chính sách & Hướng dẫn');
        $this->migrator->add('general.footer_col_3_menu_id', null);
    }
};
