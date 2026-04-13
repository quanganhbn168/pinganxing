<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản admin mặc định
        $user = User::firstOrCreate(
            ['email' => 'admin@cnetpos.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin123'),
                'phone' => '0123456789',
                'address' => 'Vietnam',
            ]
        );

        // Gán role super_admin (dùng cho Filament Shield)
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->call([
            DemoHomepageSeeder::class,
            // Thêm các seeder khác nếu cần
        ]);
    }
}
