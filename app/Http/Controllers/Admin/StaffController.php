<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\Admin;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    protected array $roleLabels = [
        'super_admin' => 'Quản trị viên (Super Admin)',
        'staff'       => 'Kỹ thuật viên (Staff)',
    ];

    public function __construct(
        protected StaffService $staffService
    ) {}

    /**
     * Danh sách nhân viên
     */
    public function index(Request $request)
    {
        $staffs = $this->staffService->list($request);
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.staff.index', compact('staffs', 'roles'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.staff.form', [
            'staff'      => null,
            'roles'      => $roles,
            'roleLabels' => $this->roleLabels,
        ]);
    }

    /**
     * Lưu nhân viên mới
     */
    public function store(StaffRequest $request)
    {
        $this->staffService->create($request->validated());

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Đã thêm nhân viên mới thành công.');
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(Admin $staff)
    {
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.staff.form', [
            'staff'      => $staff->load('roles'),
            'roles'      => $roles,
            'roleLabels' => $this->roleLabels,
        ]);
    }

    /**
     * Cập nhật thông tin
     */
    public function update(StaffRequest $request, Admin $staff)
    {
        $this->staffService->update($staff, $request->validated());

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Đã cập nhật thông tin nhân viên.');
    }

    /**
     * Xóa nhân viên (AJAX)
     */
    public function destroy(Admin $staff)
    {
        $result = $this->staffService->delete($staff);

        if (request()->ajax()) {
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Đã xóa nhân viên.']);
            }
            return response()->json(['success' => false, 'message' => 'Không thể xóa chính mình.'], 403);
        }

        if ($result) {
            return redirect()->route('admin.staff.index')->with('success', 'Đã xóa nhân viên.');
        }

        return redirect()->route('admin.staff.index')->with('error', 'Không thể xóa chính mình.');
    }

    /**
     * Toggle trạng thái (AJAX)
     */
    public function toggleStatus(Admin $staff)
    {
        $result = $this->staffService->toggleStatus($staff);

        if (request()->ajax()) {
            if ($result) {
                return response()->json([
                    'success' => true,
                    'status'  => $staff->status,
                    'message' => 'Đã cập nhật trạng thái.'
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Không thể tự khóa tài khoản của mình.'], 403);
        }

        return redirect()->back();
    }

    /**
     * Xem hiệu suất nhân viên (Task statistics)
     */
    public function performance(Request $request, Admin $staff)
    {
        // Default: tháng hiện tại
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        
        // Query tasks của nhân viên trong khoảng thời gian
        $tasksQuery = $staff->performedTasks()
            ->whereBetween('updated_at', [$from, $to . ' 23:59:59']);
        
        // Thống kê
        $stats = [
            'total'      => (clone $tasksQuery)->count(),
            'completed'  => (clone $tasksQuery)->where('status', 'completed')->count(),
            'pending'    => (clone $tasksQuery)->where('status', 'pending')->count(),
            'processing' => (clone $tasksQuery)->where('status', 'processing')->count(),
            'cancelled'  => (clone $tasksQuery)->where('status', 'cancelled')->count(),
            'collected'  => (clone $tasksQuery)->where('status', 'completed')->sum('collected_amount'),
        ];
        
        // Danh sách task với pagination
        $tasks = $tasksQuery->with('workOrder:id,code,title')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();
        
        return view('admin.staff.performance', compact('staff', 'stats', 'tasks', 'from', 'to'));
    }
}
