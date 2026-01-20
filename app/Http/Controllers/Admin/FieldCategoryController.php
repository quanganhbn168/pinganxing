<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldCategory;
use App\Http\Requests\FieldCategoryRequest;
use App\Services\FieldCategoryService;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;

class FieldCategoryController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        protected FieldCategoryService $fieldCategoryService
    ) {}

    public function index()
    {
        $categories = $this->fieldCategoryService->getAll();
        return view('admin.field_categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = FieldCategory::with('children')->whereNull('parent_id')->get();
        return view('admin.field_categories.create', compact('parentCategories'));
    }

    public function store(FieldCategoryRequest $request)
    {
        $validatedData = $request->validated();
        
        $validatedData['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'field_categories');
        $validatedData['banner_original_path'] = $this->processImageInput($request, 'banner_original_path', null, 'field_categories/banner');
        
        $this->fieldCategoryService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.field-categories.create')->with('success', 'Thêm danh mục lĩnh vực thành công.')
            : redirect()->route('admin.field-categories.index')->with('success', 'Thêm danh mục lĩnh vực thành công.');
    }

    public function edit(FieldCategory $fieldCategory) 
    {
        $parentCategories = FieldCategory::with('children')
        ->whereNull('parent_id')
        ->where('id', '!=', $fieldCategory->id)
        ->get();

        $fieldCategory->load(['parent', 'images']);
        return view('admin.field_categories.edit', compact('fieldCategory', 'parentCategories'));
    }

    public function update(FieldCategoryRequest $request, FieldCategory $fieldCategory)
    {
        $validatedData = $request->validated();
        
        // 1. Image
        $currentImage = optional($fieldCategory->mainImage())->original_path;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'field_categories');

        if ($newImage !== $currentImage) {
            $validatedData['image_original_path'] = $newImage;
        } else {
            unset($validatedData['image_original_path']);
        }

        // 2. Banner
        $currentBanner = optional($fieldCategory->bannerImage())->original_path;
        $newBanner = $this->processImageInput($request, 'banner_original_path', $currentBanner, 'field_categories/banner');

        if ($newBanner !== $currentBanner) {
            $validatedData['banner_original_path'] = $newBanner;
        } else {
            unset($validatedData['banner_original_path']);
        }
        
        $this->fieldCategoryService->update($fieldCategory, $validatedData);
        
        return redirect()->route('admin.field-categories.index')->with('success', 'Cập nhật danh mục lĩnh vực thành công.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:field_categories,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                FieldCategory::whereIn('id', $ids)->delete();
                $message = "Đã xóa thành công $count danh mục.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    public function destroy(FieldCategory $fieldCategory)
    {
        $this->fieldCategoryService->delete($fieldCategory);
        return redirect()->route('admin.field-categories.index')->with('success', 'Xóa danh mục lĩnh vực thành công.');
    }
}