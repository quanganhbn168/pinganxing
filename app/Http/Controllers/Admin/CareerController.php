<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CareerRequest;
use App\Models\Career;
use App\Services\CareerService;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;

class CareerController extends Controller
{
    use UploadImageTrait;

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
            $data = $request->validated();
            // Handle image (convertToWebp = false)
            $data['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'careers', false);

            $this->service->create($data);
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
            $data = $request->validated();
            
            // Handle image (convertToWebp = false)
            $currentImage = $career->image;
            $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'careers', false);
            
            if ($newImage !== $currentImage) {
                $data['image_original_path'] = $newImage;
            } else {
                unset($data['image_original_path']);
            }

            $this->service->update($career, $data);
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