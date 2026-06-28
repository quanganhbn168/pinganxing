<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $placeholders = [
            'general.zalo' => ['http://zalo.me', 'https://zalo.me'],
            'general.messenger' => ['http://m.me', 'https://m.me'],
            'general.youtube' => ['http://youtube.com', 'https://youtube.com', 'http://www.youtube.com', 'https://www.youtube.com'],
            'general.tiktok' => ['http://tiktok.com', 'https://tiktok.com', 'http://www.tiktok.com', 'https://www.tiktok.com'],
        ];

        foreach ($placeholders as $property => $values) {
            if (! $this->migrator->exists($property)) {
                continue;
            }

            $this->migrator->update($property, function ($value) use ($values) {
                $normalized = strtolower(rtrim(trim((string) $value), '/'));

                return in_array($normalized, $values, true) ? null : $value;
            });
        }
    }
};
