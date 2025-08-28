<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Intro;
use App\Models\Testimonial;
use App\Models\Team;
use App\Models\Product;
use App\Models\Branch;
class HomeController extends Controller
{
    public function index()
    {
        $categoriesWithProducts = Category::where('status', 1)->where('is_home',1)
            ->whereHas('products') 
            ->with(['products' => function ($query) {
                $query->where('status', 1)->where('is_home',1)->take(8);
            }])->get();
        $allCategoriesHome = Category::where('status', 1)->where('is_home',1)->get();
        $featuredCategories = Category::where('status', 1)
        ->where('is_home',1)->take(4)->get();
        $banners = Slide::where('status',1)->where("type",'4')->get();
        $products = Product::where("status",1)->get();
        $hotProducts = $products->where('is_featured',true);
        $saleProducts =  $products->where('is_on_sale',true);
        $partnerSlide = Slide::where('type',3)->where("status",1)->get();
        $slides = Slide::where('status',1)->where('type',1)->get();
        $intros = Intro::select("id","image","description","title")->get();
        $categories = Category::where('status',1)->where("is_home",1)->where("parent_id",0)->get();
        $serviceCategory = ServiceCategory::where('status', 1)->where("is_home",1)->where("parent_id",0)->get();
        $services = Service::where("status",1)->get();
        $homeCategories = PostCategory::where('status', 1)
            ->where('is_home', 1)
            ->with(['posts' => function ($q) {
                $q->where('status', 1)->latest()->limit(6);
            }])->get();
        $testimonials = Testimonial::where("status",1)->get();
        $teams = Team::get();
        $branches = Branch::get();
        return view('frontend.index', compact(
            "categoriesWithProducts",
            "featuredCategories",
            "allCategoriesHome",
            "products",
            "branches",
            "hotProducts",
            "saleProducts",
            'slides',
            'banners',
            'categories',
            'serviceCategory',
            'homeCategories',
            'intros',
            'testimonials',
            'teams',
            'services',
            'partnerSlide'
        ));
    }
    public function search(Request $request)
    {
        $keyword = trim($request->input('q'));

        if (empty($keyword)) {
            return redirect()->back();
        }

        // Tìm kiếm và phân trang trực tiếp từ database
        $products = Product::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->latest() // Sắp xếp theo ngày tạo mới nhất (tùy chọn)
            ->paginate(10); // Lấy 10 sản phẩm mỗi trang

        return view('frontend.search_result', [
            'results' => $products,
            'keyword' => $keyword
        ]);
    }
}