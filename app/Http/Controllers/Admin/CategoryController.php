<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index()
    {
        $categories = $this->categoryService->getAll();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::select("id","name",'parent_id')->get();

        return view('admin.categories.create', compact('categories'));
    }

    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
        $this->categoryService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.categories.create')->with('success', 'Thêm danh mục thành công.')
            : redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }

    public function edit(Category $category)
    {
        $category->load(['parent', 'images', 'children.childrenRecursive']);

        $excludeIds = array_merge([$category->id], $category->descendantIds());

        $categories = Category::select("id","name","parent_id")->get();
        
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
        $this->categoryService->update($category, $validatedData);
        
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }
    /**
     * Xử lý thao tác hàng loạt (Delete, Active, Inactive...)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:categories,id', // Đảm bảo ID tồn tại
            'action' => 'required|string|in:delete,active,inactive', // Các hành động cho phép
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                // Cách 1: Xóa nhanh bằng 1 lệnh SQL (Tối ưu nhất)
                // Lưu ý: Cách này sẽ KHÔNG kích hoạt Observer (nếu bạn có logic xóa ảnh trong Observer)
                // Nếu muốn xóa ảnh vật lý qua Observer, hãy dùng vòng lặp hoặc Event.
                Category::whereIn('id', $ids)->delete();
                
                $message = "Đã xóa thành công $count danh mục.";
                break;

            // Ví dụ mở rộng sau này:
            // case 'active':
            //     Category::whereIn('id', $ids)->update(['status' => 1]);
            //     $message = "Đã hiển thị $count danh mục.";
            //     break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}