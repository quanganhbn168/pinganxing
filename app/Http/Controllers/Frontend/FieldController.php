<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Project;
use App\Models\Slug;
use App\Settings\PageSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\View\View;

class FieldController extends Controller
{
    /**
     * Resolve slug cho prefix /linh-vuc/{slug}.
     */
    public function resolveBySlug(string $slug)
    {
        $slugData = Slug::where('slug', $slug)->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof FieldCategory => $this->byCategory($model),
            $model instanceof Field         => $this->detail($model),
            default => abort(404),
        };
    }

    public function index()
    {
        $pageSettings = app(PageSettings::class);
        $setting      = app(\App\Settings\GeneralSettings::class);

        $pageTitle    = $pageSettings->fields_title    ?: 'Lĩnh vực hoạt động';
        $pageSubtitle = $pageSettings->fields_headline ?: null;
        $bannerUrl    = $this->resolveMediaUrl($pageSettings->fields_banner)
            ?? ($setting->banner ?? asset('images/setting/no-banner.png'));
        $breadcrumbs  = [['label' => $pageTitle]];

        $field_categories = FieldCategory::query()
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('parent_id', 0)->orWhereNull('parent_id');
            })
            ->with([
                'image',
                'fields' => function ($query) {
                    $query->where('status', 1)
                        ->with(['image', 'category'])
                        ->orderByDesc('is_featured')
                        ->latest();
                },
            ])
            ->orderByDesc('is_home')
            ->orderBy('position')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $featuredFieldCategory = $field_categories->firstWhere('is_home', true) ?? $field_categories->first();

        $featuredFields = Field::query()
            ->where('status', 1)
            ->where('is_featured', 1)
            ->with(['image', 'category'])
            ->latest()
            ->take(8)
            ->get();

        if ($featuredFields->isEmpty()) {
            $featuredFields = $field_categories
                ->flatMap(fn (FieldCategory $category) => $category->fields)
                ->unique('id')
                ->take(8)
                ->values();
        }

        $relatedProjectIds = collect($featuredFieldCategory?->related_project_ids ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $relatedProjects = collect();

        if ($relatedProjectIds->isNotEmpty()) {
            $relatedProjects = Project::query()
                ->where('status', 1)
                ->whereIn('id', $relatedProjectIds)
                ->with(['image', 'category'])
                ->get()
                ->sortBy(fn (Project $project) => $relatedProjectIds->search($project->id))
                ->values();
        }

        return view('frontend.fields.index', compact(
            "field_categories", "featuredFieldCategory", "featuredFields", "relatedProjects", "setting", "pageSettings",
            "pageTitle", "pageSubtitle", "bannerUrl", "breadcrumbs"
        ));
    }
    public function byCategory(FieldCategory $fieldCategory): View
    {
        $pageTitle = $fieldCategory->name;
        $current_category = $fieldCategory;
        $childCategories = $fieldCategory->children()->where('status', 1)->get();        

        $setting = app(\App\Settings\GeneralSettings::class);
        $bannerUrl = $fieldCategory->image ? $fieldCategory->image->url : asset('images/setting/no-banner.png');
        $breadcrumbs = [
            ['label' => 'Lĩnh vực', 'url' => route('frontend.fields.index')],
            ['label' => $pageTitle, 'url' => '']
        ];

        if ($childCategories->isNotEmpty()) {
            return view("frontend.fields.fieldByCate", [
                "field_categories" => $childCategories,
                "pageTitle" => $pageTitle,
                "current_category" => $current_category,
                "setting" => $setting,
                "bannerUrl" => $bannerUrl,
                "breadcrumbs" => $breadcrumbs
            ]);
        }        
        $fields = $fieldCategory->fields()->where('status', 1)->paginate(10);
        return view("frontend.fields.fieldList", compact("fields", "pageTitle", "current_category", "setting", "bannerUrl", "breadcrumbs"));
    }
    public function detail(Field $field): View
    {
        $pageTitle = $field->name;
        $breadcrumbs = [];

        $currentCategory = $field->category;

        while ($currentCategory) {
            array_unshift($breadcrumbs, $currentCategory);
            
            $currentCategory = $currentCategory->parent;
        }

        return view("frontend.fields.detail", compact("field", "pageTitle", "breadcrumbs"));
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
}
