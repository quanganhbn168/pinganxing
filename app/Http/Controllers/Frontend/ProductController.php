<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    /**
     * Hiển thị trang tất cả sản phẩm với nền tảng cho việc lọc.
     */
    public function index(Request $request)
    {
        $query = Product::query()->with('category');
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->has('price_min') && $request->has('price_max')) {
            $query->whereBetween('price', [$request->price_min, $request->price_max]);
        }
        if ($request->has('sort_by')) {
            if ($request->sort_by == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort_by == 'price_desc') {
                $query->orderBy('price', 'desc');
            }
        } else {
            $query->latest(); 
        }
        $products = $query->paginate(12);
        $categories = Category::where('status', true)->get();
        $tags = Tag::all();
        return view('frontend.products.allProduct', compact('products', 'categories', 'tags'));
    }
    /**
     * Hiển thị sản phẩm theo một danh mục cụ thể.
     */
    public function byCategory(Category $category)
    {
        $descendantIds = $category->getAllDescendantIds();
        $categoryIds = collect($descendantIds)->push($category->id);
        $products = Product::whereIn('category_id', $categoryIds)
                            ->with('category')
                            ->where("status",1)
                            ->orderByDesc('id')
                            ->paginate(12);
        $categories = Category::where('status', true)->whereNull('parent_id')->get();
        $tags = Tag::all();
        return view('frontend.products.productByCate', compact('products', 'categories', 'tags', 'category'));
    }
    /**
     * Hiển thị chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        $product->load('category', 'images', 'variants.attributeValues.attribute');
        $variantAttributes = [];
            foreach ($product->variants as $variant) {
                foreach ($variant->attributeValues as $attributeValue) {
                    $attributeName = $attributeValue->attribute->name;
                    $valueId = $attributeValue->id;
                    $value = $attributeValue->value;
                    if (!isset($variantAttributes[$attributeName])) {
                        $variantAttributes[$attributeName] = [];
                    }
                    $variantAttributes[$attributeName][$valueId] = $value;
                }
            }
        $variantMap = $product->variants
        ->filter(fn($variant) => $variant->attributeValues->isNotEmpty()) 
        ->mapWithKeys(function ($variant) {
            $key = $variant->attributeValues->pluck('id')->sort()->implode('-');
            return [$key => [
                'id' => $variant->id,
                'price' => $variant->price_discount ?? $variant->price, 
                'compare_at_price' => $variant->price, 
                'sku' => $variant->code, 
                'stock' => $variant->stock,
            ]];
        });
        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->inRandomOrder()
            ->take(4)
            ->get();
        return view('frontend.products.detail', compact('product', 'relatedProducts','variantAttributes','variantMap'));
    }
}