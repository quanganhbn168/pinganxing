<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\Team;
use App\Models\Product;
use App\Models\FieldCategory;
use App\Models\Career;
use App\Models\ProjectCategory;
use App\Models\Partner;
use App\Models\Brand;
use App\Models\TourCategory;
use App\Settings\GeneralSettings;
use App\Settings\HomeSettings;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::where("status", 1)->with('image')->orderBy('position')->get();
        
        $homeProducts = \App\Models\Tour::where("status", 1)->where("is_home", 1)
            ->with(['image', 'category.image', 'tags'])
            ->get();
            
        $homeCategories = \App\Models\TourCategory::where("status", 1)->where("is_home", 1)
            ->with('image')
            ->orderBy('position')
            ->get();

        $destinationCategories = TourCategory::query()
            ->where('status', 1)
            ->whereHas('tours', fn ($query) => $query->where('status', 1))
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
            
        $homeServicesCategories = ServiceCategory::where("status", 1)
            ->where("is_home", 1)
            ->with(['image', 'banner'])
            ->orderBy('position')
            ->get();
        $homeProjectCategories = ProjectCategory::where("status", 1)->where("is_home", 1)->with([
            "projects" => function ($query) {
                $query->where("status", 1)->with(['image', 'category']);
            }
        ])->orderBy('position')->get();
        $homeFields = FieldCategory::where("parent_id", 0)
            ->where("status", 1)
            ->with([
                'fields' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->get();
        $homeProjects = $homeProjectCategories->pluck('projects')->flatten();
        $allPosts = Post::where('status', 1)->with(['image', 'category'])->latest()->take(4)->get();
        $homePostCategories = PostCategory::where('status', 1)
            ->where('is_home', 1)
            ->with([
                'posts' => function ($query) {
                    $query->where('status', 1)->latest()->take(3);
                }
            ])
            ->get();
        $careers = Career::get();
        $brands = Brand::where('status', 1)->with('image')->orderBy('id')->get();
        $setting = app(GeneralSettings::class);
        $homeSettings = app(HomeSettings::class);

        // Resolve Curator media IDs → URLs
        if (!empty($homeSettings->intro_image)) {
            $media = \Awcodes\Curator\Models\Media::find($homeSettings->intro_image);
            $homeSettings->intro_image = $media ? $media->url : null;
        }
        if (!empty($homeSettings->video_file)) {
            $media = \Awcodes\Curator\Models\Media::find($homeSettings->video_file);
            $homeSettings->video_file = $media ? $media->url : null;
        }

        $testimonials = Testimonial::where('status', 1)->with('image')->get();

        return view('frontend.index', compact(
            "slides",
            "allPosts",
            "homeProducts",
            "homeCategories",
            "destinationCategories",
            "homeServicesCategories",
            "homeProjectCategories",
            "homeFields",
            "homeProjects",
            "homePostCategories",
            "careers",
            "testimonials",
            "brands",
            "setting",
            "homeSettings"
        ));
    }

    public function search(Request $request)
    {
        $keyword = trim($request->input('q'));

        if (empty($keyword)) {
            return redirect()->back();
        }

        $products = Product::where('status', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->latest()
            ->paginate(10);

        return view('frontend.products.search_results', [
            'results' => $products,
            'keyword' => $keyword
        ]);
    }

    public function postSearch(Request $request)
    {
        $destination = trim((string) $request->input('destination'));

        if ($destination === '') {
            return redirect()->route('frontend.tours.index');
        }

        $category = TourCategory::query()
            ->where('status', 1)
            ->whereHas('tours', fn ($query) => $query->where('status', 1))
            ->where(function ($query) use ($destination) {
                $query->where('slug', $destination)
                    ->orWhere('name', $destination);
            })
            ->first();

        return $category
            ? redirect()->route('frontend.tours.category', ['slug' => $category->slug_value])
            : redirect()->route('frontend.tours.index');
    }
}
