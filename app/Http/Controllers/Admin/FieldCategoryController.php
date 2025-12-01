<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldCategory;
use App\Http\Requests\FieldCategoryRequest;
use App\Services\FieldCategoryService;
use Illuminate\Http\Request;
class FieldCategoryController extends Controller
{
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
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
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
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
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