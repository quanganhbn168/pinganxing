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
use Illuminate\Support\Collection;
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
        $project->loadMissing(['image', 'banner', 'category', 'slugData']);

        // Lấy các dự án liên quan (trừ dự án đang xem)
        $relatedProjects = Project::with(['image', 'category', 'slugData'])
            ->where("status", 1)
            ->where("id", '!=', $project->id)
            ->when($project->project_category_id, fn ($query) => $query->where('project_category_id', $project->project_category_id))
            ->latest()
            ->limit(6)
            ->get();

        if ($relatedProjects->count() < 6) {
            $fallbackProjects = Project::with(['image', 'category', 'slugData'])
                ->where('status', 1)
                ->where('id', '!=', $project->id)
                ->whereNotIn('id', $relatedProjects->pluck('id'))
                ->latest()
                ->limit(6 - $relatedProjects->count())
                ->get();

            $relatedProjects = $relatedProjects->concat($fallbackProjects)->values();
        }

        $setting = app(\App\Settings\GeneralSettings::class);
        $pageSettings = app(PageSettings::class);
        $pageTitle = $project->name;
        $bannerUrl = $project->banner?->url
            ?? $project->image?->url
            ?? ($setting->banner ?? asset('images/setting/no-banner.png'));
        $breadcrumbs = [
            ['label' => 'Dự án', 'url' => route('frontend.projects.index')],
            ['label' => $project->name, 'url' => ''],
        ];

        $images = $this->projectGalleryImages($project);
        $projectOverview = $project->project_overview ?: $project->description;
        $businessProblems = $this->caseStudyItems($project->business_problems);
        $implementedSolutions = $this->caseStudyItems($project->implemented_solutions);
        $implementationProcess = $this->caseStudyItems($project->implementation_process);
        $achievedResults = $this->caseStudyItems($project->achieved_results, ['value', 'label', 'description']);
        $projectInfo = collect([
            $project->investor ? ['label' => 'Chủ đầu tư', 'value' => $project->investor, 'icon' => 'fas fa-building'] : null,
            $project->address ? ['label' => 'Địa điểm', 'value' => $project->address, 'icon' => 'fas fa-location-dot'] : null,
            $project->year ? ['label' => 'Năm thực hiện', 'value' => $project->year, 'icon' => 'fas fa-calendar-check'] : null,
            $project->value ? ['label' => 'Quy mô', 'value' => is_numeric($project->value) ? number_format((float) $project->value, 0, ',', '.') . ' VNĐ' : $project->value, 'icon' => 'fas fa-chart-line'] : null,
        ])->filter()->values();
        $metaDescription = \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 155);

        return view("frontend.projects.detail", compact(
            "project",
            "relatedProjects",
            "pageTitle",
            "bannerUrl",
            "breadcrumbs",
            "setting",
            "pageSettings",
            "images",
            "projectOverview",
            "businessProblems",
            "implementedSolutions",
            "implementationProcess",
            "achievedResults",
            "projectInfo",
            "metaDescription"
        ));
    }

    private function caseStudyItems(mixed $items, array $keys = ['title', 'description', 'icon']): Collection
    {
        return collect($items ?? [])
            ->filter(fn ($item) => is_array($item))
            ->filter(function (array $item) use ($keys) {
                foreach ($keys as $key) {
                    if (filled($item[$key] ?? null)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    private function projectGalleryImages(Project $project): Collection
    {
        $images = collect();

        $push = function (?string $url) use ($images): void {
            if (blank($url)) {
                return;
            }

            $normalized = $this->normalizeImageUrl($url);

            if ($normalized && ! $images->contains($normalized)) {
                $images->push($normalized);
            }
        };

        $push($project->image?->url);

        foreach (collect($project->gallery)->filter() as $item) {
            $push($this->resolveGalleryItemUrl($item));
        }

        if ($images->isEmpty()) {
            $push($project->banner?->url);
        }

        return $images->values();
    }

    private function resolveGalleryItemUrl(mixed $item): ?string
    {
        if ($item instanceof Media) {
            return $item->url;
        }

        if (is_numeric($item)) {
            return Media::find((int) $item)?->url;
        }

        if (is_string($item)) {
            return $item;
        }

        if (is_array($item)) {
            $id = $item['id'] ?? $item['media_id'] ?? null;

            if (is_numeric($id)) {
                $mediaUrl = Media::find((int) $id)?->url;

                if ($mediaUrl) {
                    return $mediaUrl;
                }
            }

            return $item['url'] ?? $item['path'] ?? null;
        }

        return null;
    }

    private function normalizeImageUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://', '//'])) {
            return $url;
        }

        return asset(ltrim($url, '/'));
    }
}
