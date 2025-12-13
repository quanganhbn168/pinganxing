<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffService
{
    /**
     * Lấy danh sách nhân viên có phân trang và tìm kiếm
     */
    public function list(Request $request)
    {
        $query = Admin::query()->with('roles');

        // Tìm kiếm theo tên, email hoặc số điện thoại
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Lọc theo status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->orderByDesc('id')->paginate(15)->withQueryString();
    }

    /**
     * Tạo nhân viên mới
     */
    public function create(array $data): Admin
    {
        $staff = Admin::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'status'   => $data['status'] ?? true,
        ]);

        // Gán role
        if (!empty($data['role'])) {
            $staff->syncRoles([$data['role']]);
        }

        return $staff;
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(Admin $staff, array $data): Admin
    {
        $updateData = [
            'name'   => $data['name'],
            'phone'  => $data['phone'],
            'email'  => $data['email'],
            'status' => $data['status'] ?? $staff->status,
        ];

        // Chỉ update password nếu có nhập
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $staff->update($updateData);

        // Sync role
        if (!empty($data['role'])) {
            $staff->syncRoles([$data['role']]);
        }

        return $staff;
    }

    /**
     * Xóa nhân viên
     */
    public function delete(Admin $staff): bool
    {
        // Không cho phép xóa chính mình
        if ($staff->id === Auth::guard('admin')->id()) {
            return false;
        }

        $staff->roles()->detach();
        return $staff->delete();
    }

    /**
     * Toggle trạng thái Active/Block
     */
    public function toggleStatus(Admin $staff): bool
    {
        // Không cho phép tự block chính mình
        if ($staff->id === Auth::guard('admin')->id()) {
            return false;
        }

        $staff->status = !$staff->status;
        $staff->save();

        return true;
    }
}
