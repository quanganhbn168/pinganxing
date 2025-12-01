<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = CareerApplication::with('career')->orderBy('created_at', 'desc');

        // Lọc theo từ khóa (Tên, Email, SĐT)
        if ($request->filled('keyword')) {
            $k = $request->keyword;
            $query->where(function($q) use ($k) {
                $q->where('name', 'like', "%{$k}%")
                  ->orWhere('email', 'like', "%{$k}%")
                  ->orWhere('phone', 'like', "%{$k}%");
            });
        }

        // Lọc theo vị trí tuyển dụng
        if ($request->filled('career_id')) {
            $query->where('career_id', $request->career_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(20);
        
        // Lấy danh sách các vị trí để đổ vào dropdown lọc
        $careers = Career::select('id', 'name')->get();

        return view('admin.career_applications.index', compact('applications', 'careers'));
    }

    public function show($id)
    {
        $application = CareerApplication::with('career')->findOrFail($id);
        
        // Tự động đánh dấu là "Đã xem" nếu đang là "pending"
        if($application->status == 'pending') {
            $application->update(['status' => 'reviewed']);
        }

        return view('admin.career_applications.show', compact('application'));
    }

    public function update(Request $request, $id)
    {
        // Hàm này dùng để cập nhật trạng thái nhanh (nếu cần)
        $app = CareerApplication::findOrFail($id);
        $app->update(['status' => $request->status]);
        return back()->with('success', 'Đã cập nhật trạng thái hồ sơ.');
    }

    public function destroy($id)
    {
        $application = CareerApplication::findOrFail($id);

        // 1. Xóa file CV vật lý để tiết kiệm dung lượng
        if ($application->cv_path && Storage::disk('public')->exists($application->cv_path)) {
            Storage::disk('public')->delete($application->cv_path);
        }

        // 2. Xóa database
        $application->delete();

        return redirect()->route('admin.career-applications.index')->with('success', 'Đã xóa hồ sơ ứng tuyển.');
    }
}