<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ProjectService;
use App\Models\ProjectCategory;
use App\Http\Requests\ProjectRequest;
class ProjectController extends Controller
{
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
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
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
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
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