<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.services_description', 'Hệ sinh thái phần mềm quản trị chuyên sâu, đáp ứng chuẩn mực nghiệp vụ cho đa dạng ngành nghề.');
        $this->migrator->add('general.fields_description', 'Nền tảng ERP của chúng tôi được thiết kế linh hoạt, đáp ứng giải pháp chuyên sâu cho từng ngành nghề đặc thù.');
        $this->migrator->add('general.projects_description', 'Những dự án công nghệ và công trình tiêu biểu được tín nhiệm bởi các đối tác.');
    }
};
