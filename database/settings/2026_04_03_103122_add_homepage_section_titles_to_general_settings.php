<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.services_title', 'Dịch Vụ Cung Cấp');
        $this->migrator->add('general.fields_title', 'Lĩnh Vực Hoạt Động');
        $this->migrator->add('general.projects_title', 'Dự Án Tiêu Biểu');
    }
};
