<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            MenuSeeder::class,
            TourCategorySeeder::class,
            TourSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            SlideSeeder::class,
        ]);
    }
}
