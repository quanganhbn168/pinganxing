<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $pages = [
            'products' => null,
            'projects' => 'Dự án triển khai tiêu biểu',
            'services' => null,
            'fields' => 'Giải pháp công nghệ toàn diện',
            'posts' => null,
            'careers' => null,
            'contact' => null,
            'agency' => null,
            'consulting' => null,
        ];

        foreach ($pages as $key => $defaultSubline) {
            $this->migrator->add("page.{$key}_leaderboard_subline", $defaultSubline);
            $this->migrator->add("page.{$key}_leaderboard_description", null);
            $this->migrator->add("page.{$key}_leaderboard_actions", []);
            $this->migrator->add("page.{$key}_leaderboard_stats", []);
        }
    }
};
