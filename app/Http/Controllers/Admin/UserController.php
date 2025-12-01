<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // [MỚI] Nếu dùng Global Bulk Action thì không cần trait này nữa.
    // Nhưng nếu muốn custom logic xóa user (check đơn hàng), thì viết riêng.
    
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Lấy các role của guard 'web' để gán cho user thường
        // Nếu bạn muốn nhân viên cũng login bằng bảng users, thì lấy role admin.
        // Nhưng theo model của bạn (Admin riêng, User riêng), thì User chỉ nên có role 'Khách hàng', 'Thợ'...
        $roles = Role::where('guard_name', 'web')->get();
        
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email',
            'phone'    => 'required|string|unique:users,phone', // SĐT là bắt buộc và duy nhất
            'password' => 'required|string|min:6|confirmed',
            'roles'    => 'nullable|array'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'password' => Hash::make($request->password),
        ]);

        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công.');
    }

    public function edit(User $user)
    {
        $roles = Role::where('guard_name', 'web')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email,'.$user->id,
            'phone'    => 'required|string|unique:users,phone,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'roles'    => 'nullable|array'
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sync Roles
        if ($request->has('roles')) { // Nếu có gửi mảng roles (kể cả rỗng)
            $user->syncRoles($request->roles);
        } else {
            // Trường hợp form không gửi roles lên (bỏ tick hết), ta xóa hết role
            $user->syncRoles([]); 
        }

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy(User $user)
    {
        // Kiểm tra logic nghiệp vụ (Vd: User có đơn hàng chưa hoàn thành thì không xóa)
        if ($user->orders()->exists()) {
            return back()->with('error', 'Người dùng này đã có đơn hàng, không thể xóa.');
        }

        $user->delete();
        return back()->with('success', 'Đã xóa người dùng.');
    }
}