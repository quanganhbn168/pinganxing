<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Unit;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $service;
    public function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $services = Service::with('category')->latest()->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::all();
        return view('admin.services.create', compact('categories'));
    }

    public function store(Request $request)
    {

        $this->service->create($request);
        return $request->input('action') === 'save_new'
            ? redirect()->route('admin.services.create')->with('success', 'Đã thêm dịch vụ, tiếp tục thêm mới.')
            : redirect()->route('admin.services.index')->with('success', 'Đã thêm dịch vụ.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::get(['id', 'parent_id', 'name'])->toArray();
        $images = $service->images->pluck('image')->toArray();
        return view('admin.services.edit', compact('service', 'categories','images'));
    }
    public function show(Service $service)
    {
        $categories = ServiceCategory::get(['id', 'parent_id', 'name'])->toArray();
        $images = $service->images->pluck('image')->toArray();
        return view('admin.services.edit', compact('service', 'categories','images'));
    }
    
    public function update(Request $request, Service $service)
    {
        $this->service->update($request, $service);
        return redirect()->route('admin.services.index')->with('success', 'Cập nhật dịch vụ thành công.');
    }

    public function destroy(Service $service)
    {
        $this->service->delete($service);
        return redirect()->back()->with('success', 'Xoá dịch vụ thành công.');
    }

    public function serviceByCate($slug)
    {
        $category = ServiceCategory::where("slug", $slug)->where("status", 1)->firstOrFail();

        if ($category->parent_id == 0) {
            // Danh mục cha → lấy toàn bộ service của các danh mục con
            $childCategoryIds = $category->children()->pluck('id');

            $services = Service::whereIn('service_category_id', $childCategoryIds)
                ->where('status', 1)
                ->get();
        } else {
            // Danh mục con → lấy service theo id hiện tại
            $services = Service::where('service_category_id', $category->id)
                ->where('status', 1)
                ->get();
        }

        return view("frontend.services.serviceByCate", compact("category", "services"));
    }


    public function detail(Service $service)
    {
        $relatedService = Service::where("status", 1)
        ->where("id", '!=', $service->id)
        ->orderBy("updated_at", "DESC")
        ->limit(6)
        ->get();

        return view("frontend.services.detail", compact("service", "relatedService"));
    }

}