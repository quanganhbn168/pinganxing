<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\AttributeService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $productService;
    protected $attributeService;

    public function __construct(ProductService $productService, AttributeService $attributeService)
    {
        $this->productService = $productService;
        $this->attributeService = $attributeService;
    }

    public function validateUniqueness(Request $request)
    {
        $productId = $request->input('productId');
        $errors = [];

        $codeValidator = Validator::make($request->only('code'), [
            'code' => ['required', 'string', Rule::unique('products', 'code')->ignore($productId)],
        ]);

        if ($codeValidator->fails()) {
            $errors['code'] = $codeValidator->errors()->get('code');
        }

        $skuErrors = [];
        $skusData = $request->input('skus', []);
        if (!empty($skusData)) {
            foreach ($skusData as $item) {
                $sku = $item['sku'];
                $variantId = $item['id'];
                $skuValidator = Validator::make(['sku' => $sku], [
                    'sku' => ['required', 'string', Rule::unique('product_variants', 'sku')->ignore($variantId)],
                ]);
                if ($skuValidator->fails()) {
                    if (!isset($skuErrors[$sku])) {
                        $skuErrors[$sku] = $skuValidator->errors()->first('sku');
                    }
                }
            }
        }

        if (!empty($skuErrors)) {
            $errors['skus'] = $skuErrors;
        }

        if (empty($errors)) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'errors' => $errors]);
    }

    /**
     * EM ĐÃ SỬA LẠI PHƯƠNG THỨC NÀY
     * Nó chỉ trả về view, không lấy dữ liệu nữa.
     */
    public function index()
    {
        return view('admin.products.index');
    }

    public function create()
    {
        $attributes = $this->productService->getAttribute();
        $categories = Category::pluck('name', 'id')->toArray();
        $brands = Brand::pluck('name', 'id')->toArray();
        return view('admin.products.create', compact('categories', 'brands','attributes'));
    }

    public function store(ProductRequest $request, ProductService $productService)
    {
        $product = $productService->store($request);
        return redirect()->route('admin.products.edit',$product->id)->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function edit(Product $product)
    {
        $brands = Brand::pluck('name', 'id')->toArray();
        $attributes = $this->attributeService->getAttributesWithValues();
        $categories = Category::where('status', true)->pluck('name', 'id')->toArray();
        $product->load(['variants.attributeValues']);
        return view('admin.products.edit', compact('product', 'brands', 'attributes', 'categories'));
    }

    public function update(ProductRequest $request, Product $product, ProductService $productService)
    {
        $productService->update($request, $product);
        return redirect()->back()->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }

    /**
     * Cung cấp dữ liệu cho Datatables.
     * Phương thức này anh đã thêm đúng rồi.
     */
    public function data(Request $request)
        {
            if ($request->ajax()) {
                // Eager load quan hệ 'category' để tối ưu truy vấn
                $query = Product::with('category');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('image', function ($product) {
                        // Giả sử $product->image là đường dẫn tương đối tới ảnh
                        // Nếu không có ảnh thì hiển thị ảnh mặc định
                        $imageUrl = $product->image ? asset($product->image) : asset('path/to/default-image.png');
                        return '<img src="'.$imageUrl.'" alt="Ảnh SP" width="50" class="img-thumbnail">';
                    })
                    ->addColumn('category', function ($product) {
                        // Lấy tên category qua relationship, nếu không có thì hiển thị 'N/A'
                        return $product->category->name ?? 'N/A';
                    })
                    ->addColumn('status', function ($product) {
                        $class = $product->status ? 'success' : 'danger';
                        $text = $product->status ? 'Hiện' : 'Ẩn';
                        $modelClass = str_replace('\\', '\\\\', Product::class);

                        return '<span class="badge badge-'.$class.' boolean-toggle"
                                      data-model="'.$modelClass.'"
                                      data-id="'.$product->id.'"
                                      data-field="status"
                                      style="cursor: pointer;">'
                                      .$text.
                               '</span>';
                    })
                    ->addColumn('is_home', function ($product) {
                        $class = $product->is_home ? 'success' : 'danger';
                        $text = $product->is_home ? 'Hiện' : 'Ẩn';
                        $modelClass = str_replace('\\', '\\\\', Product::class);

                        return '<span class="badge badge-'.$class.' boolean-toggle"
                                      data-model="'.$modelClass.'"
                                      data-id="'.$product->id.'"
                                      data-field="is_home"
                                      style="cursor: pointer;">'
                                      .$text.
                               '</span>';
                    })
                    
                    ->addColumn('action', function($row){
                        $editUrl = route('admin.products.edit', $row->id);
                        $btn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm mr-1">Sửa</a>';
                        $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-id="'.$row->id.'">Xóa</button>';
                        return $btn;
                    })
                    // Thêm 'image' vào rawColumns để hiển thị được thẻ <img>
                    ->rawColumns(['image', 'status', 'action','is_home'])
                    ->make(true);
            }
        }
}