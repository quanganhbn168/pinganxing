<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.working_hours', 'T2 - T7: 08:00 - 17:30');
    }
};
