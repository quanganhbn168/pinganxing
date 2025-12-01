<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;

class RoleController extends Controller
{
    // Helper lấy config
    private function getSystemPermissions()
    {
        return [
            'modules' => config('system_permissions.modules', []),
            'actions' => config('system_permissions.actions', []),
        ];
    }

    public function index()
    {
        // Lấy danh sách Role kèm số lượng người dùng đang nắm giữ vai trò đó
        // Lưu ý: model_has_roles không có quan hệ trực tiếp trong Eloquent chuẩn của Spatie
        // nên ta dùng withCount('users') nếu đã setup quan hệ trong model Admin/User
        // Hoặc đơn giản lấy all() trước
        $roles = Role::orderBy('id', 'asc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $data = $this->getSystemPermissions();
        return view('admin.roles.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            // [MỚI] Validate guard_name
            'guard_name' => 'required|in:admin,web',
            'permissions' => 'nullable|array'
        ]);
        
        // Kiểm tra trùng tên trong cùng 1 guard
        $exists = Role::where('name', $request->name)
                      ->where('guard_name', $request->guard_name)
                      ->exists();
                      
        if ($exists) {
            return back()->withInput()->with('error', "Vai trò '{$request->name}' đã tồn tại trong nhóm {$request->guard_name}.");
        }

        // [MỚI] Lưu guard_name động theo select
        $role = Role::create([
            'name' => $request->name, 
            'guard_name' => $request->guard_name
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Tạo vai trò thành công.');
    }

    public function edit($id)
    {
        $role = Role::findById($id, 'admin');
        
        // Không cho sửa Super Admin để tránh lỗi hệ thống
        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Không thể chỉnh sửa Super Admin.');
        }

        $data = $this->getSystemPermissions();
        $data['role'] = $role;
        // Lấy danh sách tên quyền đã có để check vào checkbox
        $data['rolePermissions'] = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findById($id, 'admin');

        if ($role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Không thể chỉnh sửa Super Admin.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,'.$id.',id,guard_name,admin',
            'permissions' => 'nullable|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò thành công.');
    }

    public function destroy($id)
    {
        $role = Role::findById($id, 'admin');

        if ($role->name === 'Super Admin') {
            return back()->with('error', 'Không thể xóa Super Admin.');
        }

        $role->delete();
        return back()->with('success', 'Đã xóa vai trò.');
    }
}