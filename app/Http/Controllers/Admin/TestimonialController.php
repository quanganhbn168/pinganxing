<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestimonialRequest;
use App\Models\Testimonial;
use App\Services\TestimonialService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function __construct(
        protected TestimonialService $testimonialService
    ) {}

    /**
     * Danh sách testimonial + bộ lọc & phân trang
     * Filters: keyword (title/link), status
     * Params: per_page (mặc định 20)
     */
    public function index(Request $request)
    {
        [$testimonials] = $this->testimonialService->list($request);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /** Form tạo */
    public function create()
    {
        return view('admin.testimonials.create');
    }

    /** Lưu tạo */
    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();
        // media-input trả về path ở input hidden "image_original_path"
        $data['image_original_path'] = $request->input('image_original_path');

        $this->testimonialService->create($data);

        return $request->has('save_new')
            ? redirect()->route('admin.testimonials.create')->with('success', 'Thêm testimonial thành công.')
            : redirect()->route('admin.testimonials.index')->with('success', 'Thêm testimonial thành công.');
    }

    /** Form sửa */
    public function edit(Testimonial $testimonial)
    {
        // để view prefill media-input
        $testimonial->load('images');
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    /** Cập nhật */
    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $data = $request->validated();
        $data['image_original_path'] = $request->input('image_original_path');

        $this->testimonialService->update($testimonial, $data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Cập nhật testimonial thành công.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:testimonials,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                Testimonial::whereIn('id', $ids)->delete();
                $message = "Đã xóa thành công $count testimonial.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    /** Xoá */
    public function destroy(Testimonial $testimonial)
    {
        $this->testimonialService->delete($testimonial);
        return redirect()->route('admin.testimonials.index')->with('success', 'Đã xoá testimonial.');
    }
}
