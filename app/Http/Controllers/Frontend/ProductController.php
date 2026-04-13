<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slug;
use App\Settings\PageSettings;

class ProductController extends Controller
{
    /**
     * Resolve slug cho prefix /san-pham/{slug}.
     * Tự phân biệt Product (detail) vs Category (listing).
     */
    public function resolveBySlug(string $slug, Request $request)
    {
        $slugData = Slug::where('slug', $slug)->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof Category => $this->byCategory($model, $request),
            $model instanceof Product  => $this->show($model),
            default => abort(404),
        };
    }

    /**
     * Hiển thị trang danh sách sản phẩm theo từng root category (Option A)
     */
    public function index(Request $request)
    {
        $pageSettings = app(PageSettings::class);
        $perCategoryLimit = (int) $request->input('limit', 12);
        $allCategories = Category::whereNull('parent_id')->where('status', 1)->orderBy('name')->get();
        
        $categories = Category::where('status', 1)
            ->orderBy('name')
            ->get();

        
        $childrenMap = [];
        foreach ($categories as $c) {
            $parent = $c->parent_id ?? 0;
            $childrenMap[$parent][] = $c->id;
        }

        
        $collectDescendants = function ($startId) use (&$childrenMap, &$collectDescendants) {
            $ids = [$startId];
            $children = $childrenMap[$startId] ?? [];
            foreach ($children as $childId) {
                $ids = array_merge($ids, $collectDescendants($childId));
            }
            return $ids;
        };

        
        $roots = $categories->whereNull('parent_id')->values();

        
        $allProducts = Product::where('status', 1)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->get();

        
        $productsByCategory = [];
        foreach ($allProducts as $p) {
            $productsByCategory[$p->category_id ?? 0][] = $p;
        }

        
        $allCategoryAndProduct = collect();
        foreach ($roots as $root) {
            $descIds = $collectDescendants($root->id);

            $col = collect();
            foreach ($descIds as $id) {
                if (!empty($productsByCategory[$id])) {
                    $col = $col->merge($productsByCategory[$id]);
                }
            }

            
            $col = $col->slice(0, $perCategoryLimit);

            
            $obj = (object) [
                'category' => $root,
                'products' => $col,
            ];

            $allCategoryAndProduct->push($obj);
        }

        return view('frontend.products.index', compact('allCategoryAndProduct', 'pageSettings', 'allCategories'));
    }
    /**
     * Route cho trang danh mục, sẽ chuyển hướng logic về hàm index.
     */
    public function byCategory(Category $category, Request $request)
    {
        
        $collectIds = function ($rootId) {
            $ids = [$rootId];
            $stack = [$rootId];

            while (!empty($stack)) {
                $parent = array_pop($stack);
                $children = Category::where('parent_id', $parent)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

                foreach ($children as $c) {
                    if (!in_array($c, $ids, true)) {
                        $ids[] = $c;
                        $stack[] = $c;
                    }
                }
            }

            return $ids;
        };

        
        $categoryIds = $collectIds($category->id);
        
        $childCategories = Category::where('parent_id', $category->id)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        
        $otherCategories = Category::where('status', 1)
            ->whereNotIn('id', $categoryIds)
            ->orderBy('name')
            ->get();

        
        $products = Product::where('status', 1)
            ->whereIn('category_id', $categoryIds)
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->except('page')); 
        
        $featuredProducts = Product::where('status', 1)
            ->whereIn('category_id', $categoryIds)
            ->where('is_featured', 1)
            ->inRandomOrder()
            ->get();

        return view('frontend.products.productByCate', compact(
            'category',
            'products',
            'otherCategories',
            'featuredProducts',
            'childCategories'
        ));
    }

    /**
     * Hiển thị chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->inRandomOrder()
            ->take(4)
            ->get();
        return view('frontend.products.detail', compact('product', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('q');

    
    
        $products = Product::where('name', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%") 
                ->where('status',1) 
                ->paginate(12); 

    
    
                return view('frontend.products.search_results', [
                    'products' => $products,
                    'keyword' => $keyword,
                    'pageTitle' => 'Kết quả tìm kiếm: ' . $keyword
                ]);
            }
}
