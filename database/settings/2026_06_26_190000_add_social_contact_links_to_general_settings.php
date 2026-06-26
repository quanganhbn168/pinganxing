<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        foreach (['facebook', 'whatsapp', 'wechat'] as $key) {
            if (! $this->migrator->exists("general.{$key}")) {
                $this->migrator->add("general.{$key}", null);
            }
        }
    }
};
