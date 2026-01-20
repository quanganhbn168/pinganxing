<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SlideRequest;
use App\Models\Slide;
use App\Services\SlideService;
use App\Enums\SliderType; // <--- QUAN TRỌNG: Import Enum ở đây
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;

class SlideController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        protected SlideService $slideService
    ) {}

    /**
     * Danh sách slide + bộ lọc & phân trang
     */
    public function index(Request $request)
    {
        [$slides] = $this->slideService->list($request);
        
        // Lấy danh sách Type để dùng cho bộ lọc bên View (nếu cần)
        $types = SliderType::cases(); 

        return view('admin.slides.index', compact('slides', 'types'));
    }

    /** Form tạo */
    public function create()
    {
        // Lấy danh sách Type để hiển thị Select box
        $types = SliderType::cases(); 

        // Truyền biến $types sang view create
        return view('admin.slides.create', compact('types'));
    }

    /** Lưu tạo */
    public function store(SlideRequest $request)
    {
        $data = $request->validated();
        
        // Handle image (convertToWebp = false)
        $data['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'slides', false);

        $this->slideService->create($data);

        return $request->has('save_new')
            ? redirect()->route('admin.slides.create')->with('success', 'Thêm slide thành công.')
            : redirect()->route('admin.slides.index')->with('success', 'Thêm slide thành công.');
    }

    /** Form sửa */
    public function edit(Slide $slide)
    {
        // để view prefill media-input
        $slide->load('images');

        // Lấy danh sách Type
        $types = SliderType::cases(); 

        // Truyền biến $types sang view edit
        return view('admin.slides.edit', compact('slide', 'types'));
    }

    /** Cập nhật */
    public function update(SlideRequest $request, Slide $slide)
    {
        $data = $request->validated();
        
        // Handle image (convertToWebp = false)
        $currentImage = optional($slide->mainImage())->original_path ?? $slide->image;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'slides', false);
        
        if ($newImage !== $currentImage) {
            $data['image_original_path'] = $newImage;
        } else {
            unset($data['image_original_path']);
        }

        $this->slideService->update($slide, $data);

        return redirect()->route('admin.slides.index')->with('success', 'Cập nhật slide thành công.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:slides,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                // Xóa nhanh
                Slide::whereIn('id', $ids)->delete();
                $message = "Đã xóa thành công $count slide.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    /** Xoá */
    public function destroy(Slide $slide)
    {
        $this->slideService->delete($slide);
        return redirect()->route('admin.slides.index')->with('success', 'Đã xoá slide.');
    }
}