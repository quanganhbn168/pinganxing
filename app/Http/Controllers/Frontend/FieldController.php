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
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
                'faqs' => function ($query) {
                    $query->active();
                },
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

        $overviewDescription = $pageSettings->fields_description ?? $setting->fields_description ?? null;
        $showcaseFields = $featuredFieldCategory?->fields ?? collect();
        $showcaseBullets = $showcaseFields->pluck('name')->filter()->take(5)->values();

        if ($showcaseBullets->isEmpty()) {
            $showcaseBullets = collect([
                'Chuẩn hóa nghiệp vụ theo mô hình vận hành thực tế',
                'Kết nối dữ liệu bán hàng, nhân sự, kho và tài chính',
                'Tối ưu báo cáo quản trị theo thời gian thực',
                'Mở rộng linh hoạt theo quy mô doanh nghiệp',
            ]);
        }

        $businessChallenges = $this->landingItems($featuredFieldCategory?->business_challenges);
        $cnetposSolutions = $this->landingItems($featuredFieldCategory?->cnetpos_solutions);
        $keyFeatures = $this->landingItems($featuredFieldCategory?->key_features);
        $processSteps = $this->landingItems($featuredFieldCategory?->implementation_steps);
        $impactStats = $this->landingItems($featuredFieldCategory?->impact_stats, ['value', 'label']);

        if ($businessChallenges->isEmpty()) {
            $businessChallenges = $showcaseBullets->take(4)->map(fn (string $title) => ['title' => $title, 'description' => null]);
        }

        if ($cnetposSolutions->isEmpty()) {
            $cnetposSolutions = $showcaseBullets->take(4)->map(fn (string $title) => ['title' => $title, 'description' => null]);
        }

        if ($keyFeatures->isEmpty()) {
            $keyFeatures = $showcaseBullets->take(5)->map(fn (string $title) => [
                'icon' => 'fas fa-layer-group',
                'title' => $title,
                'description' => null,
            ]);
        }

        if ($processSteps->isEmpty()) {
            $processSteps = collect([
                ['title' => 'Khảo sát & Tư vấn', 'description' => 'Hiểu đặc thù vận hành và mục tiêu doanh nghiệp.', 'icon' => 'fas fa-clipboard-check'],
                ['title' => 'Đề xuất giải pháp', 'description' => 'Phân tích lộ trình, phạm vi và cấu hình phù hợp.', 'icon' => 'fas fa-lightbulb'],
                ['title' => 'Ký kết & Chuẩn bị', 'description' => 'Thống nhất phương án, kế hoạch triển khai.', 'icon' => 'fas fa-file-signature'],
                ['title' => 'Triển khai & Đào tạo', 'description' => 'Cài đặt, cấu hình và đào tạo đội ngũ sử dụng.', 'icon' => 'fas fa-chalkboard-user'],
                ['title' => 'Chạy thử & Nghiệm thu', 'description' => 'Kiểm thử, tối ưu và nghiệm thu giải pháp.', 'icon' => 'fas fa-circle-check'],
                ['title' => 'Vận hành & Hỗ trợ', 'description' => 'Đồng hành, hỗ trợ 24/7 và phát triển lâu dài.', 'icon' => 'fas fa-headset'],
            ]);
        }

        if ($impactStats->isEmpty()) {
            $impactStats = collect([
                ['value' => '+35%', 'label' => 'Doanh thu bình quân'],
                ['value' => '-25%', 'label' => 'Thời gian kiểm kho'],
                ['value' => '-30%', 'label' => 'Hao hụt hàng hóa'],
                ['value' => '+50%', 'label' => 'Hiệu suất nhân viên'],
            ]);
        }

        $processSteps = $processSteps
            ->values()
            ->map(fn (array $step, int $index) => array_merge($step, [
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            ]));

        $faqs = $this->faqItems($featuredFieldCategory?->faqs ?? collect());

        if ($faqs->isEmpty()) {
            $faqs = collect([
                ['question' => 'CNETPOS có phù hợp với doanh nghiệp nhỏ không?', 'answer' => 'Có. Giải pháp có thể cấu hình theo quy mô hiện tại và mở rộng khi doanh nghiệp phát triển.'],
                ['question' => 'Chi phí triển khai được tính như thế nào?', 'answer' => 'Chi phí phụ thuộc vào phạm vi nghiệp vụ, số điểm vận hành, thiết bị và mức độ tích hợp cần triển khai.'],
                ['question' => 'Thời gian triển khai giải pháp là bao lâu?', 'answer' => 'Thông thường từ vài tuần tùy mô hình vận hành, dữ liệu hiện có và mức độ tùy biến.'],
                ['question' => 'Doanh nghiệp có được hướng dẫn sử dụng không?', 'answer' => 'Đội ngũ CNETPOS đào tạo, bàn giao tài liệu và hỗ trợ trong quá trình vận hành thực tế.'],
            ]);
        }

        $fieldCategoryCards = $field_categories
            ->values()
            ->map(fn (FieldCategory $category, int $index) => $this->categoryCard($category, $index))
            ->values();

        $projectsForTabs = Project::query()
            ->where('status', 1)
            ->whereHas('category', fn ($query) => $query->where('status', 1))
            ->with(['image', 'category', 'slugData'])
            ->orderByDesc('is_home')
            ->latest()
            ->get();

        $projectTabPanels = $projectsForTabs
            ->filter(fn (Project $project) => $project->category)
            ->groupBy(fn (Project $project) => $project->category->id)
            ->map(function (Collection $projects) {
                $category = $projects->first()->category;

                return [
                    'id' => 'project-panel-' . $category->id,
                    'name' => $category->name,
                    'sort_key' => sprintf(
                        '%d-%010d-%s',
                        $category->is_home ? 0 : 1,
                        $category->position ?? 0,
                        $category->name
                    ),
                    'cards' => $this->projectCards($projects->take(6)),
                ];
            })
            ->filter(fn (array $panel) => $panel['cards']->isNotEmpty())
            ->sortBy('sort_key')
            ->map(fn (array $panel) => [
                'id' => $panel['id'],
                'name' => $panel['name'],
                'cards' => $panel['cards'],
            ])
            ->values();

        $allProjectCards = $this->projectCards($projectsForTabs->take(12));

        $storyField = $showcaseFields->first();
        $storyFieldCard = $storyField ? $this->fieldCard($storyField, $featuredFieldCategory) : null;
        $showcaseImage = $featuredFieldCategory?->image?->url ?: 'https://placehold.co/720x720/eaf4fb/0e4a86?text=CNETPOS';
        $showcaseDescription = Str::limit(strip_tags((string) ($featuredFieldCategory?->solution_overview ?: $featuredFieldCategory?->description ?: $featuredFieldCategory?->content ?: $overviewDescription)), 280);

        return view('frontend.fields.index', compact(
            'field_categories',
            'featuredFieldCategory',
            'setting',
            'pageSettings',
            'pageTitle',
            'pageSubtitle',
            'bannerUrl',
            'breadcrumbs',
            'overviewDescription',
            'fieldCategoryCards',
            'showcaseImage',
            'showcaseDescription',
            'storyFieldCard',
            'businessChallenges',
            'cnetposSolutions',
            'keyFeatures',
            'processSteps',
            'impactStats',
            'allProjectCards',
            'projectTabPanels',
            'faqs',
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

    private function landingItems(mixed $items, array $keys = ['title', 'description']): Collection
    {
        return collect($items ?? [])
            ->filter(function ($item) use ($keys) {
                if (! is_array($item)) {
                    return false;
                }

                foreach ($keys as $key) {
                    if (filled($item[$key] ?? null)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    private function faqItems(mixed $items): Collection
    {
        return collect($items ?? [])
            ->map(fn ($faq) => is_array($faq)
                ? $faq
                : ['question' => $faq->question ?? null, 'answer' => $faq->answer ?? null])
            ->filter(fn (array $item) => filled($item['question'] ?? null) || filled($item['answer'] ?? null))
            ->values();
    }

    private function categoryCard(FieldCategory $category, int $index = 0): array
    {
        return [
            'url' => $category->slug_url,
            'image' => $category->image?->url ?: 'https://placehold.co/720x720/eaf4fb/0e4a86?text=Industry',
            'title' => $category->name,
            'description' => Str::limit(strip_tags((string) ($category->description ?: $category->content)), 110),
            'delay' => min($index * 80, 320),
        ];
    }

    private function fieldCard(Field $field, ?FieldCategory $fallbackCategory = null): array
    {
        $category = $field->category ?? $fallbackCategory;

        return [
            'url' => $field->slug_url,
            'image' => $field->image?->url ?: ($category?->image?->url ?? 'https://placehold.co/420x420/eaf4fb/0e4a86?text=CNETPOS'),
            'title' => $field->name,
            'badge' => $category?->name ?? 'Lĩnh vực',
        ];
    }

    private function projectCards(iterable $projects): Collection
    {
        return collect($projects)
            ->map(fn (Project $project) => [
                'url' => $project->slug_url,
                'image' => $project->image?->url ?: 'https://placehold.co/520x360/eaf4fb/0e4a86?text=Project',
                'title' => $project->name,
                'badge' => $project->category?->name ?? 'Dự án',
                'description' => Str::limit(strip_tags((string) $project->description), 90),
            ])
            ->values();
    }
}
