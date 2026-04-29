<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Slug;
use App\Settings\GeneralSettings;
use App\Settings\PageSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function byCategory(ProjectCategory $category)
    {
        return $this->index(request(), $category);
    }

    public function index(Request $request, ?ProjectCategory $category = null)
    {
        $pageSettings = app(PageSettings::class);
        $setting      = app(GeneralSettings::class);

        $baseTitle = $pageSettings->projects_title ?: ($setting->projects_title ?? 'Dự án');
        $pageTitle = $category?->name ?: $baseTitle;
        $pageSubtitle = $category?->description ?: ($pageSettings->projects_headline ?: 'Những dự án triển khai tiêu biểu, câu chuyện vận hành và hiệu quả chuyển đổi từ CNETPOS.');
        $bannerUrl = $category?->banner?->url
            ?? $category?->image?->url
            ?? $this->resolveMediaUrl($pageSettings->projects_banner)
            ?? ($setting->banner ?? null);
        $breadcrumbs = $category
            ? [['label' => 'Dự án', 'url' => route('frontend.projects.index')], ['label' => $pageTitle]]
            : [['label' => $pageTitle]];

        $keyword = trim((string) $request->input('q', ''));
        $sort = $request->input('sort', 'latest');
        $activeCategory = $category;
        $categoryIds = $category ? ProjectCategory::getTreeIds($category->id) : null;

        $projectCategories = ProjectCategory::where(function ($query) {
                $query->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->where('status', 1)
            ->withCount(['projects' => fn ($query) => $query->where('status', 1)])
            ->orderBy('position')
            ->get();

        $featureQuery = Project::with(['image', 'banner', 'category', 'slugData'])
            ->where('status', 1)
            ->when($categoryIds, fn ($query) => $query->whereIn('project_category_id', $categoryIds));

        $projectFeature = (clone $featureQuery)->where('is_home', 1)->latest()->first()
            ?? (clone $featureQuery)->latest()->first();

        $query = Project::with(['image', 'category', 'slugData'])
            ->where('status', 1)
            ->when($categoryIds, fn ($query) => $query->whereIn('project_category_id', $categoryIds));

        if ($keyword !== '') {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('content', 'LIKE', "%{$keyword}%")
                    ->orWhere('investor', 'LIKE', "%{$keyword}%")
                    ->orWhere('address', 'LIKE', "%{$keyword}%");
            });
        }

        match ($sort) {
            'oldest' => $query->oldest(),
            'featured' => $query->orderByDesc('is_home')->latest(),
            default => $query->latest(),
        };

        if ($projectFeature) {
            $query->where('id', '!=', $projectFeature->id);
        }
        $projects = $query->paginate(8)->withQueryString();

        $topProjects = Project::with(['image', 'category', 'slugData'])
            ->where('status', 1)
            ->when($categoryIds, fn ($query) => $query->whereIn('project_category_id', $categoryIds))
            ->when($projectFeature, fn ($query) => $query->where('id', '!=', $projectFeature->id))
            ->latest()
            ->take(2)
            ->get();

        $popularProjects = Project::with(['image', 'category', 'slugData'])
            ->where('status', 1)
            ->latest('updated_at')
            ->take(5)
            ->get();

        $metaDescription = Str::limit(strip_tags($category?->description ?: ($pageSettings->projects_description ?? $setting->projects_description ?? '')), 155);

        return view('frontend.projects.index', compact(
            'projectFeature', 'projects', 'setting', 'pageSettings',
            'pageTitle', 'pageSubtitle', 'bannerUrl', 'breadcrumbs', 'metaDescription',
            'projectCategories', 'topProjects', 'popularProjects', 'keyword', 'sort',
            'activeCategory'
        ));
    }

    private function resolveMediaUrl(mixed $settingValue): ?string
    {
        if (empty($settingValue)) {
            return null;
        }

        if (is_string($settingValue) && str_starts_with($settingValue, '[') && str_ends_with($settingValue, ']')) {
            $decoded = json_decode($settingValue, true);
            if (is_array($decoded)) {
                $settingValue = $decoded;
            }
        }

        $id = is_array($settingValue) ? ($settingValue[0] ?? null) : $settingValue;
        $media = is_numeric($id) ? Media::find((int) $id) : null;

        return $media?->url ? url($media->url) : null;
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
        $relatedProjects = Project::with(['image', 'slugData'])
            ->where("status", 1)
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

        $images = collect();
        if ($project->gallery && is_array($project->gallery)) {
            $first = reset($project->gallery);
            if (is_numeric($first)) {
                $medias = \Awcodes\Curator\Models\Media::whereIn('id', $project->gallery)->get();
                foreach ($medias as $media) {
                    $images->push($media->path);
                }
            } else {
                foreach ($project->gallery as $galImg) {
                    $url = is_string($galImg) ? $galImg : ($galImg['url'] ?? $galImg['path'] ?? null);
                    if ($url) {
                        $images->push($url);
                    }
                }
            }
        }
        $images = $images->filter()->values();
        $metaDescription = \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 155);

        return view("frontend.projects.detail", compact(
            "project",
            "relatedProjects",
            "pageTitle",
            "bannerUrl",
            "breadcrumbs",
            "setting",
            "images",
            "metaDescription"
        ));
    }
}
