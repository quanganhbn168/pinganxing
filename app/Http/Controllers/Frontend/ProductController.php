<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Slug;
use App\Models\Attribute;
use App\Settings\PageSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Resolve slug cho prefix /san-pham/{slug}.
     * Tự phân biệt Product (detail) vs Category (listing).
     */
    public function resolveBySlug(string $slug, Request $request)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->whereIn('sluggable_type', [Category::class, Product::class])
            ->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof Category => $this->byCategory($model, $request),
            $model instanceof Product  => $this->show($model),
            default => abort(404),
        };
    }

    public function productBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->where('sluggable_type', Product::class)
            ->firstOrFail();

        return $this->show($slugData->sluggable);
    }

    public function categoryBySlug(string $slug, Request $request)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->where('sluggable_type', Category::class)
            ->firstOrFail();

        return $this->byCategory($slugData->sluggable, $request);
    }

    /**
     * Hiển thị trang danh sách sản phẩm theo từng root category (Option A)
     */
    public function index(Request $request)
    {
        $pageSettings = app(PageSettings::class);
        $perCategoryLimit = (int) $request->input('limit', 12);
        $allCategories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        $allBrands = Brand::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        $hasFilters = $this->hasProductFilters($request);

        if ($hasFilters) {
            $products = $this->filteredProductsQuery($request)
                ->with(['image', 'category', 'brand'])
                ->paginate(20)
                ->appends($request->query());

            return view('frontend.products.index', compact(
                'products',
                'pageSettings',
                'allCategories',
                'allBrands',
                'hasFilters'
            ));
        }
        
        $categories = $allCategories;

        
        $childrenMap = [];
        foreach ($categories as $c) {
            $parent = $c->parent_id ?: 0;
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

        
        $roots = $categories->filter(fn($c) => empty($c->parent_id))->values();

        
        $allProducts = Product::where('status', 1)
            ->with(['image', 'category', 'brand'])
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

        return view('frontend.products.index', compact('allCategoryAndProduct', 'pageSettings', 'allCategories', 'allBrands', 'hasFilters'));
    }
    /**
     * Route cho trang danh mục, sẽ chuyển hướng logic về hàm index.
     */
    public function byCategory(Category $category, Request $request)
    {
        $pageSettings = app(PageSettings::class);
        
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

        
        $products = $this->filteredProductsQuery($request)
            ->whereIn('category_id', $categoryIds)
            ->with(['image', 'category', 'brand'])
            ->paginate(12)
            ->appends($request->query()); 
        
        $featuredProducts = Product::where('status', 1)
            ->whereIn('category_id', $categoryIds)
            ->where('is_featured', 1)
            ->inRandomOrder()
            ->get();

        $allCategories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $allBrands = Brand::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('frontend.products.productByCate', compact(
            'category',
            'products',
            'otherCategories',
            'featuredProducts',
            'childCategories',
            'pageSettings',
            'allCategories',
            'allBrands'
        ));
    }

    private function filteredProductsQuery(Request $request)
    {
        $query = Product::query()->where('status', 1);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $categoryIds = Category::getTreeIds((int) $request->input('category_id'));
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', (int) $request->input('brand_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->input('type'));
        }

        match ($request->input('price')) {
            'under-2m' => $query->where('price', '>', 0)->where('price', '<', 2000000),
            '2m-5m' => $query->whereBetween('price', [2000000, 5000000]),
            '5m-10m' => $query->whereBetween('price', [5000000, 10000000]),
            'over-10m' => $query->where('price', '>', 10000000),
            'contact' => $query->where(function ($query) {
                $query->whereNull('price')->orWhere('price', '<=', 0);
            }),
            default => null,
        };

        match ($request->input('sort')) {
            'price-asc' => $query->orderByRaw('CASE WHEN price IS NULL OR price <= 0 THEN 1 ELSE 0 END')->orderBy('price'),
            'price-desc' => $query->orderByDesc('price'),
            'name-asc' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query;
    }

    private function hasProductFilters(Request $request): bool
    {
        return collect(['q', 'category_id', 'brand_id', 'type', 'price', 'sort'])
            ->contains(fn (string $key) => filled($request->input($key)));
    }

    /**
     * Hiển thị chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        $product->load(['variants', 'image', 'banner', 'category', 'brand']);

        $productImages = $this->productGalleryImages($product);

        $variantOptions = [];
        $variantMatrix = [];
        $defaultVariantId = null;

        if ($product->has_variants) {
            $variants = $product->variants
                ->filter(fn ($variant) => is_array($variant->options) && ! empty($variant->options))
                ->values();

            $attributeIds = $variants
                ->flatMap(fn ($variant) => array_keys($variant->options ?? []))
                ->filter(fn ($key) => is_numeric($key))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $attributeNames = Attribute::query()
                ->whereIn('id', $attributeIds)
                ->pluck('name', 'id');

            foreach ($variants as $variant) {
                $normalizedOptions = [];
                foreach (($variant->options ?? []) as $key => $value) {
                    if (! is_numeric($key) || ! filled($value)) {
                        continue;
                    }

                    $attributeId = (int) $key;
                    $normalizedOptions[(string) $attributeId] = (string) $value;
                }

                if (empty($normalizedOptions)) {
                    continue;
                }

                ksort($normalizedOptions, SORT_NATURAL);
                $signature = collect($normalizedOptions)->map(fn ($value, $key) => "{$key}={$value}")->join('|');
                $variantMatrix[$signature] = [
                    'id' => $variant->id,
                    'price' => (float) $variant->price,
                    'compare_at_price' => $variant->compare_at_price !== null ? (float) $variant->compare_at_price : null,
                    'stock' => (int) $variant->stock,
                    'sku' => $variant->sku,
                ];

                if ($variant->is_default && $defaultVariantId === null) {
                    $defaultVariantId = $variant->id;
                }

                foreach ($normalizedOptions as $attributeId => $optionValue) {
                    $attributeName = $attributeNames[(int) $attributeId] ?? ('Thuộc tính ' . $attributeId);
                    $variantOptions[$attributeId]['name'] = $attributeName;
                    $variantOptions[$attributeId]['values'][] = $optionValue;
                }
            }

            foreach ($variantOptions as $attributeId => $meta) {
                $variantOptions[$attributeId]['values'] = collect($meta['values'] ?? [])
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['image', 'category'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('frontend.products.detail', compact(
            'product',
            'relatedProducts',
            'variantOptions',
            'variantMatrix',
            'defaultVariantId',
            'productImages',
        ));
    }

    private function productGalleryImages(Product $product): Collection
    {
        $images = collect();

        $push = function (?string $url) use ($images): void {
            if (blank($url)) {
                return;
            }

            $normalized = $this->normalizeImageUrl($url);

            if ($normalized && ! $images->contains($normalized)) {
                $images->push($normalized);
            }
        };

        // Anh muốn ảnh đại diện luôn là ảnh đầu tiên trong gallery chi tiết.
        $push($product->image?->url);

        foreach (collect($product->gallery)->filter() as $item) {
            $push($this->resolveGalleryItemUrl($item));
        }

        if ($images->isEmpty()) {
            $push($product->banner?->url);
        }

        return $images->values();
    }

    private function resolveGalleryItemUrl(mixed $item): ?string
    {
        if ($item instanceof Media) {
            return $item->url;
        }

        if (is_numeric($item)) {
            return Media::find((int) $item)?->url;
        }

        if (is_string($item)) {
            return $item;
        }

        if (is_array($item)) {
            $id = $item['id'] ?? $item['media_id'] ?? null;

            if (is_numeric($id)) {
                $mediaUrl = Media::find((int) $id)?->url;

                if ($mediaUrl) {
                    return $mediaUrl;
                }
            }

            return $item['url'] ?? $item['path'] ?? null;
        }

        return null;
    }

    private function normalizeImageUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://', '//'])) {
            return $url;
        }

        return asset(ltrim($url, '/'));
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
