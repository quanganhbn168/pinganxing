<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Page;
use App\Models\FieldCategory;
use Illuminate\View\View;
class FieldController extends Controller
{
    public function index()
    {
        $field_categories = FieldCategory::where("status",1)->whereNull("parent_id")->get();
        $page = Page::where('slug', 'linh-vuc')->first();
        $pageTitle = $page ? $page->title : "Lĩnh vực hoạt động";
        return view('frontend.fields.index',compact("field_categories","pageTitle", "page"));
    }
    public function byCategory(FieldCategory $fieldCategory): View
    {
        $pageTitle = $fieldCategory->name;
        $current_category = $fieldCategory;
        $childCategories = $fieldCategory->children()->where('status', 1)->get();        
        if ($childCategories->isNotEmpty()) {
            return view("frontend.fields.fieldByCate", [
                "field_categories" => $childCategories,
                "pageTitle" => $pageTitle,
                "current_category" => $current_category 
            ]);
        }        
        $fields = $fieldCategory->fields()->where('status', 1)->paginate(10);
        return view("frontend.fields.fieldList", compact("fields", "pageTitle", "current_category"));
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