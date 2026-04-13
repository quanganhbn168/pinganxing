<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $pages = [
            'products' => 'Sản phẩm',
            'projects' => 'Dự án',
            'services' => 'Dịch vụ',
            'fields' => 'Lĩnh vực',
            'posts' => 'Tin tức',
            'intro' => 'Giới thiệu',
            'careers' => 'Tuyển dụng',
            'contact' => 'Liên hệ'
        ];

        foreach ($pages as $key => $title) {
            $this->migrator->add("page.{$key}_title", $title);
            $this->migrator->add("page.{$key}_headline", null);
            $this->migrator->add("page.{$key}_description", '');
            $this->migrator->add("page.{$key}_content", null);
            $this->migrator->add("page.{$key}_banner", null);
        }
    }
};
