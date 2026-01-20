<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

use App\Traits\UploadImageTrait;

class ProductController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Chỉ kiểm tra uniqueness cho mã sản phẩm (code).
     * ĐÃ BỎ phần kiểm tra SKU biến thể.
     */
    public function validateUniqueness(Request $request)
    {
        $productId = $request->input('productId');

        $validator = Validator::make($request->only('code'), [
            'code' => ['required', 'string', Rule::unique('products', 'code')->ignore($productId)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => ['code' => $validator->errors()->get('code')],
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {
        
        [$products, $filterCategories] = $this->productService->list($request);

        return view('admin.products.index', compact('products', 'filterCategories'));
    }

    public function create()
    {
        
        $categories = Category::select("id","name","parent_id")->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {

        $data = $request->validated();
        
        // Xử lý ảnh (convertToWebp = false)
        $data['image_original_path'] = $this->processImageInput($request, 'image_original_path', null, 'products', false);
        
        $data['gallery_original_paths'] = $request->input('gallery_original_paths');     

        $product = $this->productService->create($data);

        return $request->has('save_new')
            ? redirect()->route('admin.products.create')->with('success', 'Thêm sản phẩm mới thành công.')
            : redirect()->route('admin.products.edit', $product->id)->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function edit(Product $product)
    {
        $categories = Category::select("id","name","parent_id")->get();
        
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        // 1. Image (convertToWebp = false)
        $currentImage = optional($product->mainImage())->original_path ?? $product->image;
        $newImage = $this->processImageInput($request, 'image_original_path', $currentImage, 'products', false);

        if ($newImage !== $currentImage) {
            $data['image_original_path'] = $newImage;
        } else {
            unset($data['image_original_path']);
        }

        $data['gallery_original_paths'] = $request->input('gallery_original_paths');

        $this->productService->update($product, $data);

        return redirect()->back()->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(Product $product)
    {
        
        
        

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    /**
     * Datatables (giữ nguyên, nhưng lấy ảnh từ hệ media mới nếu có).
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with('category');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('image', function ($product) {
                    
                    $img   = method_exists($product, 'mainImage') ? $product->mainImage() : null;
                    $url   = $img ? ($img->url('thumbnail') ?: $img->url()) : null;
                    $url ??= $product->image ? asset($product->image) : asset('images/no-image.png');

                    return '<img src="'.$url.'" alt="Ảnh SP" width="50" class="img-thumbnail">';
                })
                ->addColumn('category', fn($product) => $product->category->name ?? 'N/A')
                ->addColumn('status', function ($product) {
                    $class = $product->status ? 'success' : 'danger';
                    $text  = $product->status ? 'Hiện' : 'Ẩn';
                    $modelClass = str_replace('\\', '\\\\', Product::class);

                    return '<span class="badge badge-'.$class.' boolean-toggle"
                                data-model="'.$modelClass.'"
                                data-id="'.$product->id.'"
                                data-field="status"
                                style="cursor: pointer;">'.$text.'</span>';
                })
                ->addColumn('is_home', function ($product) {
                    $class = $product->is_home ? 'success' : 'danger';
                    $text  = $product->is_home ? 'Hiện' : 'Ẩn';
                    $modelClass = str_replace('\\', '\\\\', Product::class);

                    return '<span class="badge badge-'.$class.' boolean-toggle"
                                data-model="'.$modelClass.'"
                                data-id="'.$product->id.'"
                                data-field="is_home"
                                style="cursor: pointer;">'.$text.'</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.products.edit', $row->id);
                    $btn  = '<a href="'.$editUrl.'" class="btn btn-primary btn-sm mr-1">Sửa</a>';
                    $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'">Xóa</button>';
                    return $btn;
                })
                ->rawColumns(['image', 'status', 'is_home', 'action'])
                ->make(true);
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:products,id',
        'action' => 'required|string|in:delete,active,inactive' // Mở rộng sau này
    ]);

        $ids = $request->input('ids');
        $action = $request->input('action');

        switch ($action) {
            case 'delete':
            // Xóa 1 lệnh SQL duy nhất -> Tối ưu DB
            // Nếu có dùng Observer (để xóa ảnh) thì nên dùng Product::whereIn('id', $ids)->get()->each->delete();
            // Còn nếu xóa nhanh thì dùng dòng dưới:
            \App\Models\Product::whereIn('id', $ids)->delete(); 
            $message = 'Đã xóa ' . count($ids) . ' sản phẩm thành công.';
            break;
            
        // Mở rộng ví dụ:
        // case 'active':
        //    \App\Models\Product::whereIn('id', $ids)->update(['status' => 1]);
        //    break;
        }

        return back()->with('success', $message ?? 'Thao tác thành công');
    }
}
