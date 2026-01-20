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
use App\Models\Project;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\Team;
use App\Models\Product;
use App\Models\Branch;
use App\Models\FieldCategory;
use App\Models\Career;
use App\Models\ProjectCategory;
use App\Models\Partner;
use App\Models\Brand;
use App\Models\HomepageSection;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy tất cả sections active từ database
        $sections = HomepageSection::active()->ordered()->get()->keyBy('key');

        $slides         = Slide::where("status", 1)->where("type", \App\Enums\SliderType::HOME)->get();
        $slide_banners = Slide::where("status", 1)->where("type", \App\Enums\SliderType::BANNER_AD)->get();
        $introMain      = Intro::findOrFail(1);
        $homeProducts   = Product::where("status", 1)->where("is_home", 1)->get();
        $homeCategories = Category::where("status", 1)->where("is_home", 1)->get();
        $homeServices   = Service::where("status", 1)->where("is_home", 1)->get();
        $homeProjectCategories    = ProjectCategory::where("status", 1)->where("is_home", 1)->with(["projects" => function ($query) {
            $query->where("status", 1);
        }])->get();
        $homeFields = FieldCategory::whereNull("parent_id")
            ->where("status", 1)
            ->with(['fields' => function ($query) {
                $query->where('status', 1);
            }])
            ->get();
        $homeProjects = $homeProjectCategories->pluck('projects')->flatten();
        $allPosts = Post::where('status', 1)->latest()->take(3)->get();
        $homePostCategories = PostCategory::where('status', 1)
            ->where('is_home', 1) // Bật dòng này nếu danh mục tin tức có cờ is_home
            ->with(['posts' => function ($query) {
                $query->where('status', 1)
                    ->latest() // Lấy các bài viết mới nhất
                    ->take(3);     // Chỉ lấy 3 bài viết cho mỗi danh mục để vừa đủ cho grid
            }])
            ->get();
        $careers = Career::get();
        $brands = Brand::get();
        $testimonials   = Testimonial::where('status', 1)->latest('id')->get();
        $sodem = Page::where('slug', 'counter')->first()->features ?? [];
        $tuyendung = Page::where('slug','tuyen-dung')->first();
        $daily = Page::where('slug','dai-ly')->first();
        return view('frontend.index', compact(
            "sections",
            "slides",
            "tuyendung",
            "daily",
            "sodem",
            "allPosts",
            "slide_banners",
            "introMain",
            "homeProducts",
            "homeCategories",
            "homeServices",
            "homeProjectCategories",
            "homeFields",
            "homeProjects",
            "homePostCategories",
            "careers",
            "testimonials",
            "brands"
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
