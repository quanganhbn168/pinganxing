<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Services\ServiceCategoryService;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    protected $service;

    public function __construct(ServiceCategoryService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $categories = ServiceCategory::latest()->get();
        return view('admin.service_categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = ServiceCategory::get();
        return view('admin.service_categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->service->create($request);
        return $request->input('action') === 'save_new'
            ? redirect()->route('admin.service_categories.create')->with('success', 'Đã thêm danh mục, tiếp tục thêm mới.')
            : redirect()->route('admin.service_categories.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(ServiceCategory $service_category)
    {
        $categories = ServiceCategory::get(['id', 'parent_id', 'name'])->toArray();
        return view('admin.service_categories.edit', compact('service_category', 'categories'));
    }

    public function update(Request $request, ServiceCategory $service_category)
    {
        $this->service->update($request, $service_category);
        return redirect()->route('admin.service_categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(ServiceCategory $service_category)
    {
        $this->service->delete($service_category);
        return redirect()->back()->with('success', 'Xoá danh mục thành công.');
    }
}
