<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Http\Requests\ProjectCategoryRequest;
use App\Services\ProjectCategoryService;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;

class ProjectCategoryController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        protected ProjectCategoryService $projectCategoryService
    ) {}

    public function index()
    {
        $categories = $this->projectCategoryService->getAll();
        return view('admin.project_categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = ProjectCategory::with('children')->whereNull('parent_id')->get();
        return view('admin.project_categories.create', compact('parentCategories'));
    }

    public function store(ProjectCategoryRequest $request)
    {
        $validatedData = $request->validated();
        
        $validatedData['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'project_categories');
        $validatedData['banner_original_path'] = $this->processImageInput($request, 'banner_original_path', null, 'project_categories/banner');
        
        $this->projectCategoryService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.project-categories.create')->with('success', 'Thêm danh mục dự án thành công.')
            : redirect()->route('admin.project-categories.index')->with('success', 'Thêm danh mục dự án thành công.');
    }

    public function edit(ProjectCategory $projectCategory)
    {
        $parentCategories = ProjectCategory::with('children')
            ->whereNull('parent_id')
            ->where('id', '!=', $projectCategory->id)
            ->get();
        
        $projectCategory->load(['parent', 'images']);
        return view('admin.project_categories.edit', compact('projectCategory', 'parentCategories'));
    }

    public function update(ProjectCategoryRequest $request, ProjectCategory $projectCategory)
    {
        $validatedData = $request->validated();
        
        // 1. Image
        $currentImage = optional($projectCategory->mainImage())->original_path;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'project_categories');
        
        if ($newImage !== $currentImage) {
            $validatedData['image_original_path'] = $newImage;
        } else {
            unset($validatedData['image_original_path']);
        }

        // 2. Banner
        $currentBanner = optional($projectCategory->bannerImage())->original_path;
        $newBanner = $this->processImageInput($request, 'banner_original_path', $currentBanner, 'project_categories/banner');

        if ($newBanner !== $currentBanner) {
            $validatedData['banner_original_path'] = $newBanner;
        } else {
            unset($validatedData['banner_original_path']);
        }
        
        $this->projectCategoryService->update($projectCategory, $validatedData);
        
        return redirect()->route('admin.project-categories.index')->with('success', 'Cập nhật danh mục dự án thành công.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:project_categories,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                ProjectCategory::whereIn('id', $ids)->delete();
                $message = "Đã xóa thành công $count danh mục dự án.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    public function destroy(ProjectCategory $projectCategory)
    {
        $this->projectCategoryService->delete($projectCategory);
        return redirect()->route('admin.project-categories.index')->with('success', 'Xóa danh mục dự án thành công.');
    }
}