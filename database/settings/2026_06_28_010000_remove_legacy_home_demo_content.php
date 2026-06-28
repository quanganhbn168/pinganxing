<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if ($this->migrator->exists('home.counters')) {
            $this->migrator->update('home.counters', function ($value) {
                $expected = [
                    ['icon' => 'clock', 'value' => '10+', 'label' => 'Năm kinh nghiệm', 'color' => 'blue'],
                    ['icon' => 'users', 'value' => '500+', 'label' => 'Khách hàng tin dùng', 'color' => 'emerald'],
                    ['icon' => 'briefcase', 'value' => '1000+', 'label' => 'Dự án triển khai', 'color' => 'amber'],
                    ['icon' => 'globe-alt', 'value' => '30+', 'label' => 'Tỉnh thành phủ sóng', 'color' => 'violet'],
                ];

                $normalize = fn ($items) => collect($items)
                    ->map(fn ($item) => collect($item)->sortKeys()->all())
                    ->values()
                    ->all();

                return $normalize($value) === $normalize($expected)
                    ? []
                    : $value;
            });
        }

        if ($this->migrator->exists('home.intro_features')) {
            $this->migrator->update('home.intro_features', function ($value) {
                $titles = collect($value)->pluck('title')->filter()->values()->all();

                return $titles === ['Bảo Mật Cấp Doanh Nghiệp', 'Hiệu Suất Vượt Trội']
                    ? []
                    : $value;
            });
        }
    }
};
