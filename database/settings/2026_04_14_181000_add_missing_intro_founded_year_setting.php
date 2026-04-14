<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('intro.founded_year')) {
            $this->migrator->add('intro.founded_year', null);
        }
    }
};
