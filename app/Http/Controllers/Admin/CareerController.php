<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CareerRequest;
use App\Models\Career;
use App\Services\CareerService;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(protected CareerService $service) {}

    public function index(Request $request)
    {
        // Gọi hàm lấy danh sách (có lọc/phân trang) từ Service
        $careers = $this->service->getLists($request);
        return view('admin.careers.index', compact('careers'));
    }

    public function create()
    {
        return view('admin.careers.create', ['career' => new Career()]);
    }

    public function store(CareerRequest $request)
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.careers.index')->with('success', 'Thêm tin tuyển dụng thành công.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function edit(Career $career)
    {
        return view('admin.careers.edit', compact('career'));
    }

    public function update(CareerRequest $request, Career $career)
    {
        try {
            $this->service->update($career, $request->validated());
            return redirect()->route('admin.careers.index')->with('success', 'Cập nhật tin tuyển dụng thành công.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Career $career)
    {
        try {
            $this->service->delete($career->id);
            return back()->with('success', 'Xóa tin tuyển dụng thành công.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }
    
    // Nếu bạn dùng Global Bulk Action thì không cần hàm bulkAction ở đây nữa.
}