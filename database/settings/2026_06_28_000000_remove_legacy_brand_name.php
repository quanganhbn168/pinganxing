<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('general.footer_col_2_title')) {
            return;
        }

        $this->migrator->update('general.footer_col_2_title', function ($value) {
            return mb_strtolower(trim((string) $value)) === 'về cnetpos'
                ? 'Về Ping An Xing'
                : $value;
        });

        if ($this->migrator->exists('home.products_title')) {
            $this->migrator->update('home.products_title', function ($value) {
                return mb_strtolower(trim((string) $value)) === 'sản phẩm & thiết bị'
                    ? 'Những hành trình được yêu thích nhất'
                    : $value;
            });
        }
    }
};
