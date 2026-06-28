<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if ($this->migrator->exists('general.meta_description')) {
            $this->migrator->update('general.meta_description', function ($value) {
                return mb_strtolower(trim((string) $value)) === 'cnetpos platform for enterprise'
                    ? 'Ping An Xing - dịch vụ du lịch, tour, vé máy bay, khách sạn, visa và thuê xe.'
                    : $value;
            });
        }

        if ($this->migrator->exists('general.meta_keywords')) {
            $this->migrator->update('general.meta_keywords', function ($value) {
                return mb_strtolower(trim((string) $value)) === 'cms, corporate'
                    ? 'du lịch, tour, vé máy bay, khách sạn, visa, thuê xe, Ping An Xing'
                    : $value;
            });
        }
    }
};
