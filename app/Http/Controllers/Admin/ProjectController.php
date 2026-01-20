<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ProjectService;
use App\Models\ProjectCategory;
use App\Http\Requests\ProjectRequest;
use App\Traits\UploadImageTrait;

class ProjectController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        protected ProjectService $projectService
    ) {}

    /** Index: lọc + phân trang (chuẩn với view index mới) */
    public function index(Request $request)
    {
        [$projects, $filterCategories] = $this->projectService->list($request);
        return view('admin.projects.index', compact('projects', 'filterCategories'));
    }
    
    public function create()
    {
        $categories = ProjectCategory::pluck("name","id");
        return view('admin.projects.create', compact('categories'));
    }

    public function store(ProjectRequest $request) 
    {
        $validatedData = $request->validated(); 
        
        // Process images (upload/LFM) using Trait
        $validatedData['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'projects', false);
        $validatedData['banner_original_path'] = $this->processImageInput($request, 'banner_original_path', null, 'projects/banner', false);
        
        // Process gallery: Trait returns string (if LFM/JSON) or might upload if file array?
        // Note: ProjectService handles JSON decoding of gallery.
        // If it's a legacy file upload array, processImageInput currently handles SINGLE file.
        // Since we migrated view to image-picker (LFM), it sends JSON string.
        // We just pass it through.
        $validatedData['gallery_original_paths'] = $request->input('gallery_original_paths'); // Processed by Service
        
        // If legacy file upload support for gallery is needed, we'd need custom loop here.
        // Assuming partial migration to LFM first, we rely on LFM JSON string.

        $this->projectService->create($validatedData);
        return $request->has('save_new')
        ? redirect()->route('admin.projects.create')->with('success', 'Thêm dự án mới thành công.')
        : redirect()->route('admin.projects.index')->with('success', 'Thêm dự án thành công.');
    }

    public function edit(Project $project)
    {
        $categories = ProjectCategory::pluck("name","id");
        return view('admin.projects.edit', compact('project', 'categories'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $validatedData = $request->validated();

        // 1. Image
        $currentImage = optional($project->mainImage())->original_path;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'projects', false);
        
        // Only update if changed
        if ($newImage !== $currentImage) {
            $validatedData['image_original_path'] = $newImage;
        } else {
             unset($validatedData['image_original_path']);
        }

        // 2. Banner
        $currentBanner = optional($project->bannerImage())->original_path;
        $newBanner = $this->processImageInput($request, 'banner_original_path', $currentBanner, 'projects/banner', false);

        if ($newBanner !== $currentBanner) {
            $validatedData['banner_original_path'] = $newBanner;
        } else {
             unset($validatedData['banner_original_path']);
        }

        // 3. Gallery
        // Service handles logic (if null passed, it skips).
        // If we pass the SAME json, Service re-syncs (deletes all, adds all).
        // Optimization: Check if gallery changed?
        // Hard to check JSON vs DB relations efficiently here. Let Service handle it for now.
        $validatedData['gallery_original_paths'] = $request->input('gallery_original_paths');

        $this->projectService->update($project, $validatedData);
        return redirect()->route('admin.projects.index')->with('success', 'Cập nhật dự án thành công.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:projects,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                Project::whereIn('id', $ids)->delete();
                $message = "Đã xóa thành công $count dự án.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    public function destroy(Project $project)
    {
        $this->projectService->delete($project);
        return redirect()->route('admin.projects.index')->with('success', 'Xoá dự án thành công.');
    }
}