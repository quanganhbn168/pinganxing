<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
class ServiceController extends Controller
{
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
        $relatedServices = Service::where("status", 1)
        ->where("id", '!=', $service->id)
        ->orderBy("updated_at", "DESC")
        ->limit(6)
        ->get();

        return view("frontend.services.detail", compact("service", "relatedServices"));
    }
}
