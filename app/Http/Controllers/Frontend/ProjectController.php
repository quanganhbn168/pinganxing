<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Slug;
use App\Settings\GeneralSettings;
use App\Settings\PageSettings;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Resolve slug cho prefix /du-an/{slug}.
     */
    public function resolveBySlug(string $slug)
    {
        $slugData = Slug::where('slug', $slug)->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof ProjectCategory => $this->byCategory($model),
            $model instanceof Project         => $this->detail($model),
            default => abort(404),
        };
    }

    /**
     * Hiển thị trang danh mục dự án.
     */
    public function byCategory(ProjectCategory $category)
    {
        $projects = Project::where('status', 1)
            ->where('project_category_id', $category->id)
            ->latest()
            ->paginate(10);
            
        $setting = app(\App\Settings\GeneralSettings::class);
        $pageTitle = $category->name;
        $bannerUrl = $category->image ? $category->image->path : ($setting->banner ?? asset('images/setting/no-banner.png'));
        $breadcrumbs = [
            ['label' => 'Dự án', 'url' => route('frontend.projects.index')],
            ['label' => $pageTitle, 'url' => '']
        ];

        return view('frontend.projects.index', [
            'projects'       => $projects,
            'projectFeature' => $projects->first(),
            'category'       => $category,
            'setting'        => $setting,
            'pageTitle'      => $pageTitle,
            'bannerUrl'      => $bannerUrl,
            'breadcrumbs'    => $breadcrumbs
        ]);
    }

    /**
     * Hiển thị trang danh sách TẤT CẢ dự án.
     */
    public function index()
    {
        $pageSettings = app(PageSettings::class);
        $setting      = app(GeneralSettings::class);

        $pageTitle    = $pageSettings->projects_title    ?: ($setting->projects_title ?? 'Dự án tiêu biểu');
        $pageSubtitle = $pageSettings->projects_headline ?: null;
        $bannerUrl    = $setting->banner ?? asset('images/setting/no-banner.png');
        $breadcrumbs  = [['label' => $pageTitle]];

        $projectFeature = Project::where('is_home', 1)->where('status', 1)->first()
            ?? Project::where('status', 1)->first();

        $projects = Project::where('status', 1)->latest()->paginate(10);

        return view('frontend.projects.index', compact(
            'projectFeature', 'projects', 'setting',
            'pageTitle', 'pageSubtitle', 'bannerUrl', 'breadcrumbs'
        ));
    }

    /**
     * (Hàm này giữ nguyên để SlugController sử dụng)
     * Hiển thị trang CHI TIẾT một dự án.
     * @param Project $project
     * @return \Illuminate\View\View
     */
    public function detail(Project $project)
    {
        // Lấy các dự án liên quan (trừ dự án đang xem)
        $relatedProjects = Project::where("status", 1)
            ->where("id", '!=', $project->id)
            ->latest()
            ->limit(6)
            ->get();

        $setting = app(\App\Settings\GeneralSettings::class);
        $pageTitle = $project->name;
        $bannerUrl = $project->banner ? $project->banner->path : ($project->image ? $project->image->path : ($setting->banner ?? asset('images/setting/no-banner.png')));
        $breadcrumbs = [
            ['label' => 'Dự án', 'url' => route('frontend.projects.index')],
            ['label' => $project->name, 'url' => ''],
        ];

        return view("frontend.projects.detail", compact(
            "project",
            "relatedProjects",
            "pageTitle",
            "bannerUrl",
            "breadcrumbs",
            "setting"
        ));
    }
}