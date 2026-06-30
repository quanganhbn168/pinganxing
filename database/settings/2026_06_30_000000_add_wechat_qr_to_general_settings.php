<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('general.wechat_qr')) {
            $this->migrator->add('general.wechat_qr', null);
        }
    }
};
