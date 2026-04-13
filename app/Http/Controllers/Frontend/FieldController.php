<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Slug;
use App\Settings\PageSettings;
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
        $field_categories = FieldCategory::where("status",1)->whereNull("parent_id")->get();
        $pageSettings = app(PageSettings::class);
        $pageTitle = $pageSettings->fields_title ?: 'Lĩnh vực hoạt động';

        $setting = app(\App\Settings\GeneralSettings::class);
        $bannerUrl = !empty($setting->banner) ? $setting->banner : asset('images/setting/no-banner.png');
        $breadcrumbs = [
            ['label' => $pageTitle, 'url' => '']
        ];

        return view('frontend.fields.index',compact("field_categories","pageTitle", "pageSettings", "setting", "bannerUrl", "breadcrumbs"));
    }
    public function byCategory(FieldCategory $fieldCategory): View
    {
        $pageTitle = $fieldCategory->name;
        $current_category = $fieldCategory;
        $childCategories = $fieldCategory->children()->where('status', 1)->get();        

        $setting = app(\App\Settings\GeneralSettings::class);
        $bannerUrl = $fieldCategory->image ? $fieldCategory->image->path : asset('images/setting/no-banner.png');
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
}