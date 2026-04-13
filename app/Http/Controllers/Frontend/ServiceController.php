<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;

class ServiceController extends Controller
{
    /**
     * Resolve slug cho prefix /dich-vu/{slug}.
     */
    public function resolveBySlug(string $slug)
    {
        $slugData = Slug::where('slug', $slug)->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof ServiceCategory => $this->byCategory($model),
            $model instanceof Service         => $this->detail($model),
            default => abort(404),
        };
    }

    /**
     * Nhiệm vụ 1: Hiển thị trang DỊCH VỤ TỔNG.
     * Lấy tất cả danh mục và các dịch vụ con tương ứng.
     */
    public function index()
    {
        // Lấy tất cả các danh mục cha, đồng thời tải sẵn các dịch vụ con của chúng
        // để tránh lỗi N+1 query, giúp tối ưu tốc độ.
        $serviceCategories = ServiceCategory::where('status', 1)
            ->where('parent_id', 0) // Chỉ lấy danh mục cấp cao nhất
            ->with('services') // Tải sẵn các dịch vụ liên quan
            ->get();

        $pageTitle = 'Dịch vụ';
        $breadcrumbItems = [
            ['label' => 'Dịch vụ'],
        ];

        return view('frontend.services.index', compact(
            'serviceCategories',
            'pageTitle',
            'breadcrumbItems'
        ));
    }

    /**
     * Nhiệm vụ 2: Hiển thị trang DANH MỤC DỊCH VỤ CỤ THỂ.
     * Chỉ lấy các dịch vụ thuộc về danh mục này.
     *
     * @param ServiceCategory $category
     * @return \Illuminate\View\View
     */
    public function byCategory(ServiceCategory $category)
    {
        // Lấy các dịch vụ chỉ thuộc về danh mục đã cho
        $services = $category->services()->where('status', 1)->latest()->paginate(10);
        
        $pageTitle = $category->name;
        $breadcrumbItems = [
            ['label' => 'Dịch vụ', 'url' => route('frontend.services.index')],
            ['label' => $category->name],
        ];
        
        // Truyền ra view cả danh mục hiện tại và danh sách dịch vụ của nó
        return view('frontend.services.index', compact(
            'category', 
            'services',
            'pageTitle',
            'breadcrumbItems'
        ));
    }


    public function detail(Service $service)
    {
        // Danh sách dịch vụ cùng cấp
        $relatedServices = Service::where("status", 1)
        ->where("id", '!=', $service->id)
        ->where('service_category_id', $service->service_category_id)
        ->orderBy("updated_at", "DESC")
        ->limit(3)
        ->get();

        // Nạp data Landing Page (Dự án, Sản phẩm, Bài viết)
        $service->load(['projects.image', 'posts.image', 'products.image']);

        // Khởi tạo Breadcrumb
        $breadcrumbItems = [
            ['label' => 'Dịch vụ', 'url' => route('frontend.services.index')]
        ];
        if ($service->category) {
            $breadcrumbItems[] = ['label' => $service->category->name, 'url' => $service->category->slug_url];
        }
        $breadcrumbItems[] = ['label' => $service->name, 'url' => ''];

        // Banner Image
        $setting = app(\App\Settings\GeneralSettings::class);
        $bannerUrl = !empty($service->banner) ? $service->banner : (!empty($setting->banner) ? $setting->banner : asset('images/setting/no-banner.png'));

        return view("frontend.services.detail", compact(
            "service", 
            "relatedServices",
            "breadcrumbItems",
            "bannerUrl",
            "setting"
        ));
    }
}
