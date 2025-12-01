<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache quyền (quan trọng để không bị lỗi cache cũ)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Lấy cấu hình từ file config/permissions.php
        $modules = config('system_permissions.modules') ?? []; 
        $actions = config('system_permissions.actions') ?? [];
        
        if (empty($modules)) {
            $this->command->warn("Chưa có config modules trong system_permissions.php");
            return;
        }
        
        // Guard mặc định cho trang quản trị là 'admin'
        $guard = 'admin'; 

        // 3. Auto sinh Permission
        foreach ($modules as $moduleKey => $moduleName) {
            foreach ($actions as $actionKey => $actionName) {
                // Tên quyền: view_product, create_user...
                $permissionName = "{$actionKey}_{$moduleKey}";

                Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => $guard]
                );
            }
        }

        // 4. Tạo Role "Super Admin"
        $roleSuperAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => $guard]
        );

        // 5. Tạo tài khoản Admin mẫu (Nếu chưa có) để test
        $adminEmail = 'admin@gmail.com';
        $admin = Admin::where('email', $adminEmail)->first();

        if (!$admin) {
            $admin = Admin::create([
                'name'     => 'Quản Trị Viên',
                'email'    => $adminEmail,
                'password' => Hash::make('password'), // Mật khẩu: password
            ]);
        }

        // 6. Gán vai trò Super Admin cho tài khoản này
        // Vì model Admin có trait HasRoles và $guard_name = 'admin' nên nó sẽ tự hiểu
        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole($roleSuperAdmin);
        }
        
        echo "Done! Đã khởi tạo quyền và Super Admin.\n";
    }
}