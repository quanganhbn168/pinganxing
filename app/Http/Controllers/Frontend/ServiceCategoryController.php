<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;

class ServiceCategoryController extends Controller
{
    
    public function index()
    {
        $services = Service::where('status', 1)
            ->latest()
            ->paginate(12);

        
        $serviceCategories = ServiceCategory::where('status', 1)->get();

        
        $breadcrumbItems = [
            ['label' => 'Trang chủ', 'url' => url('/')],
            ['label' => 'Dịch vụ'],
        ];

        return view('frontend.services.index', compact(
            'services',
            'serviceCategories',
            'breadcrumbItems'
        ));
    }

    public function byCategory(ServiceCategory $category)
    {
        
        $services = Service::where('status', 1)
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(12);

        $serviceCategories = ServiceCategory::where('status', 1)->get();

        $breadcrumbItems = [
            ['label' => 'Trang chủ', 'url' => url('/')],
            ['label' => 'Dịch vụ', 'url' => route('frontend.services.index')],
            ['label' => $category->name],
        ];

        return view('frontend.services.index', compact(
            'services',
            'serviceCategories',
            'breadcrumbItems',
            'category'
        ));
    }
}
