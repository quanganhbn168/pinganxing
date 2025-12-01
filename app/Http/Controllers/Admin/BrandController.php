<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Requests\BrandRequest;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService
    ) {}

    public function index()
    {
        $brands = $this->brandService->getAll();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        
        $this->brandService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.brands.create')->with('success', 'Thêm thương hiệu thành công.')
            : redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công.');
    }

    public function edit(Brand $brand)
    {
        $brand->load('images');
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        
        $this->brandService->update($brand, $validatedData);
        
        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công.');
    }
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:brands,id',
            'action' => 'required|string|in:delete,active,inactive',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $count = count($ids);

        switch ($action) {
            case 'delete':
                Brand::whereIn('id', $ids)->delete();
                $message = "Đã xóa thành công $count thương hiệu.";
                break;
            
            default:
                return back()->withErrors(['message' => 'Hành động không hợp lệ.']);
        }

        return back()->with('success', $message);
    }
    public function destroy(Brand $brand)
    {
        $this->brandService->delete($brand);
        return redirect()->route('admin.brands.index')->with('success', 'Xóa thương hiệu thành công.');
    }
}