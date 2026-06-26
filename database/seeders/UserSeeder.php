<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sinh quyền trước
        \Artisan::call('shield:generate', [
            '--all' => true,
            '--option' => 'policies_and_permissions',
            '--panel' => 'admin'
        ]);

        // Tạo Admin
        $user = User::updateOrCreate(
            ['email' => 'admin@pinganxing.com'],
            [
                'name' => 'Super Admin',
                'phone' => '0812161236',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'remember_token' => \Str::random(10),
            ]
        );

        // Gán quyền Super Admin cho user này thông qua lệnh của Shield
        \Artisan::call('shield:super-admin', [
            '--user' => $user->id,
            '--panel' => 'admin'
        ]);
    }
}
