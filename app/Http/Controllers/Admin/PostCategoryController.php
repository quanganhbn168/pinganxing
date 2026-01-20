<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use App\Http\Requests\PostCategoryRequest;
use App\Services\PostCategoryService;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;

class PostCategoryController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        protected PostCategoryService $postCategoryService
    ) {}

    public function index()
    {
        $categories = $this->postCategoryService->getAll();
        return view('admin.post_categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = PostCategory::whereNull('parent_id')
        ->with('childrenRecursive')
        ->orderBy('name')
        ->get();

        return view('admin.post_categories.create', compact('parentCategories'));
    }

    public function store(PostCategoryRequest $request)
    {
        $validatedData = $request->validated();
        
        $validatedData['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'post_categories');
        $validatedData['banner_original_path'] = $this->processImageInput($request, 'banner_original_path', null, 'post_categories/banner');
        
        $this->postCategoryService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.post-categories.create')->with('success', 'Thêm danh mục bài viết thành công.')
            : redirect()->route('admin.post-categories.index')->with('success', 'Thêm danh mục bài viết thành công.');
    }

    public function edit(PostCategory $postCategory)
    {
        $parentCategories = PostCategory::with('children')
            ->whereNull('parent_id')
            ->where('id', '!=', $postCategory->id)
            ->get();
        
        $postCategory->load(['parent', 'images']);
        return view('admin.post_categories.edit', compact('postCategory', 'parentCategories'));
    }

    public function update(PostCategoryRequest $request, PostCategory $postCategory)
    {
        $validatedData = $request->validated();
        
        // 1. Image
        $currentImage = optional($postCategory->mainImage())->original_path ?? $postCategory->image;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'post_categories');

        if ($newImage !== $currentImage) {
            $validatedData['image_original_path'] = $newImage;
        } else {
            unset($validatedData['image_original_path']);
        }

        // 2. Banner
        $currentBanner = optional($postCategory->bannerImage())->original_path ?? $postCategory->banner;
        $newBanner = $this->processImageInput($request, 'banner_original_path', $currentBanner, 'post_categories/banner');

        if ($newBanner !== $currentBanner) {
            $validatedData['banner_original_path'] = $newBanner;
        } else {
            unset($validatedData['banner_original_path']);
        }
        
        $this->postCategoryService->update($postCategory, $validatedData);
        
        return redirect()->route('admin.post-categories.index')->with('success', 'Cập nhật danh mục bài viết thành công.');
    }
    /**
     * Xử lý thao tác hàng loạt (Delete, Active, Inactive...)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:post_categories,id', // Kiểm tra ID tồn tại trong bảng post_categories
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                $this->postCategoryService->bulkDelete($ids);
                $message = "Đã xóa thành công $count danh mục bài viết.";
                break;

            // Mở rộng cho tương lai:
            // case 'active':
            //     PostCategory::whereIn('id', $ids)->update(['status' => 1]);
            //     $message = "Đã kích hoạt $count danh mục.";
            //     break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    public function destroy(PostCategory $postCategory)
    {
        $this->postCategoryService->delete($postCategory);
        return redirect()->route('admin.post-categories.index')->with('success', 'Xóa danh mục bài viết thành công.');
    }
}   