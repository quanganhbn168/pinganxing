<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('intro.video_url')) {
            $this->migrator->add('intro.video_url', null);
        }

        if (! $this->migrator->exists('intro.video_thumbnail_id')) {
            $this->migrator->add('intro.video_thumbnail_id', null);
        }
    }
};
