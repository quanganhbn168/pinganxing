<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.products_title', 'Sản phẩm & Giải pháp');
        $this->migrator->add('general.products_description', 'Các module phần mềm và hạ tầng tối ưu cho doanh nghiệp.');
        $this->migrator->add('general.posts_title', 'Kiến thức chuyên môn');
        $this->migrator->add('general.posts_description', 'Phân tích và hướng dẫn nghiệp vụ.');
    }
};
