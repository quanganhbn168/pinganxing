<?php
namespace App\Services;

use App\Handlers\ImageGalleryHandler;
use App\Models\Product;
use App\Models\Category;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Traits\UploadImageTrait;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\AttributeService;

class ProductService
{
    use UploadImageTrait;

    public function __construct(
        protected ImageGalleryHandler $imageGallery,
        protected AttributeService $attributeService
    ) {}

    public function getAll(): LengthAwarePaginator
    {
        return Product::with('category')->latest()->paginate(20);
    }
    
    public function getCategoryOptions(): array
    {
        return Category::select('id', 'name', 'parent_id')->get()->toArray();
    }
    
    public function getAttribute()
    {
        return $this->attributeService->getAttributesWithValues();
    }

    /**
     * Tạo một sản phẩm mới.
     *
     * @param ProductRequest $request
     * @return Product
     * @throws \Throwable
     */
    public function store(ProductRequest $request): Product
    {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $data) {
            $data['slug'] = Str::slug($data['name']);
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), 'uploads/products', 800, 800, true);
            }
            if ($request->hasFile('banner')) {
                $data['banner'] = $this->uploadImage($request->file('banner'), 'uploads/products', 1920, 600, true);
            }
            
            $productData = $data;
            // Loại bỏ các trường liên quan đến biến thể trước khi tạo sản phẩm
            unset($productData['variants'], $productData['variant_attribute_ids'], $productData['attribute_values']);
            
            $product = Product::create($productData);
            
            $this->imageGallery->sync($product, $request, 'gallery', 'uploads/products/gallery');
            
            if (isset($data['has_variants']) && $data['has_variants']) {
                $this->syncVariants($product, $data['variants'] ?? [], $data['attribute_values'] ?? []);
            } else {
                $product->variants()->create([
                    'sku' => $data['code'] ?? null,
                    'price' => $data['price_discount'] ?? 0,
                    'compare_at_price' => $data['price'] ?? 0,
                    'stock' => 0,
                    'is_default' => true,
                ]);
            }
            
            return $product;
        });
    }

    /**
     * Cập nhật thông tin một sản phẩm đã có.
     *
     * @param ProductRequest $request
     * @param Product $product
     * @return Product
     * @throws \Throwable
     */
    public function update(ProductRequest $request, Product $product): Product
    {
        $data = $request->validated();
        
        return DB::transaction(function () use ($request, $data, $product) {
            $productData = $data;
            // Loại bỏ các trường liên quan đến biến thể trước khi cập nhật sản phẩm
            unset($productData['variants'], $productData['variant_attribute_ids'], $productData['attribute_values']);
            
            $productData['slug'] ??= Str::slug($productData['name']);

            if ($request->hasFile('image')) {
                $this->deleteImage($product->image);
                $productData['image'] = $this->uploadImage($request->file('image'), 'uploads/products', 800, 800, true);
            }
            if ($request->hasFile('banner')) {
                $this->deleteImage($product->banner);
                $productData['banner'] = $this->uploadImage($request->file('banner'), 'uploads/products', 1920, 300, true);
            }
            
            $product->update($productData);
            
            $this->imageGallery->sync($product, $request, 'gallery', 'uploads/products/gallery', 800, 800, true);
            
            if (isset($data['has_variants']) && $data['has_variants']) {
                $this->syncVariants($product, $data['variants'] ?? [], $data['attribute_values'] ?? []);
            } else {
                $product->variants()->delete();
                $product->variants()->create([
                    'sku' => $productData['code'] ?? null,
                    'price' => $productData['price_discount'] ?? 0,
                    'compare_at_price' => $productData['price'] ?? 0,
                    'stock' => 0,
                    'is_default' => true,
                ]);
            }
            
            return $product;
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $this->deleteImage($product->image);
            $this->deleteImage($product->banner);
            $this->imageGallery->deleteAll($product);
            $product->delete();
        });
    }

    /**
         * Đồng bộ (tạo, cập nhật, xóa) các biến thể của sản phẩm một cách thông minh.
         * [ĐÃ VIẾT LẠI] Logic dựa trên tổ hợp thuộc tính để tìm và cập nhật,
         * thay vì dựa vào ID, giải quyết triệt để vấn đề biến thể trùng lặp.
         *
         * @param Product $product
         * @param array $variantsData
         */
        public function syncVariants(Product $product, array $variantsData)
        {
            // 1. Lấy tất cả biến thể hiện có và tạo một map để tra cứu hiệu quả
            // Key của map là một chuỗi được tạo từ các ID giá trị thuộc tính đã sắp xếp (ví dụ: "10-25")
            $existingVariants = $product->variants()->with('attributeValues')->get();
            $existingVariantsMap = $existingVariants->keyBy(function ($variant) {
                return $variant->attributeValues->pluck('id')->sort()->implode('-');
            });

            $submittedVariantIds = [];
            $defaultVariantKey = request()->input('variants_default_key');

            // 2. Duyệt qua dữ liệu biến thể gửi từ form
            foreach ($variantsData as $canonicalKey => $variantData) {
                // Lấy và sắp xếp các ID giá trị thuộc tính từ canonicalKey
                $valueIds = collect(explode('|', $canonicalKey))
                    ->map(fn($pair) => (int)explode(':', $pair)[1])
                    ->sort()
                    ->values(); // Sử dụng values() để reset key sau khi sort

                if ($valueIds->isEmpty()) {
                    continue;
                }
                
                // Tạo key tra cứu từ các ID đã sắp xếp
                $lookupKey = $valueIds->implode('-');
                
                $variant = null;

                // 3. Tìm biến thể trong map. Nếu có thì cập nhật, không thì tạo mới.
                if ($existingVariantsMap->has($lookupKey)) {
                    // TÌM THẤY -> Cập nhật biến thể đã có
                    $variant = $existingVariantsMap->get($lookupKey);
                    $variant->update([
                        'price'            => $variantData['price'] ?? 0,
                        'compare_at_price' => $variantData['compare_at_price'] ?? 0,
                        'stock'            => $variantData['stock'] ?? 0,
                        'is_default'       => ($canonicalKey === $defaultVariantKey),
                        // Chỉ cập nhật SKU nếu người dùng có nhập, nếu không thì giữ nguyên
                        'sku'              => $variantData['sku'] ?? $variant->sku, 
                    ]);
                } else {
                    // KHÔNG TÌM THẤY -> Tạo biến thể mới
                    $sku = $variantData['sku'] ?? null;
                    if (empty($sku)) {
                        // Logic tạo SKU tự động chỉ chạy cho biến thể mới
                        $values = AttributeValue::whereIn('id', $valueIds)->orderBy('attribute_id')->get();
                        $skuSuffixes = $values->map(fn($v) => Str::upper(Str::substr(Str::slug($v->value), 0, 1)))->implode('-');
                        $sku = $product->code . '-' . $skuSuffixes;
                    }
                    
                    // Đảm bảo SKU này không bị trùng với bất kỳ biến thể nào khác trong hệ thống
                    $originalSku = $sku;
                    $counter = 2;
                    while (ProductVariant::where('sku', $sku)->exists()) {
                        $sku = $originalSku . '-' . $counter++;
                    }

                    $variant = $product->variants()->create([
                        'sku'              => $sku,
                        'price'            => $variantData['price'] ?? 0,
                        'compare_at_price' => $variantData['compare_at_price'] ?? 0,
                        'stock'            => $variantData['stock'] ?? 0,
                        'is_default'       => ($canonicalKey === $defaultVariantKey),
                    ]);

                    // Gán các giá trị thuộc tính cho biến thể mới tạo
                    $variant->attributeValues()->sync($valueIds->all());
                }

                if ($variant) {
                    $submittedVariantIds[] = $variant->id;
                }
            }

            // 4. Xóa các biến thể cũ không còn tồn tại trong dữ liệu gửi lên
            $variantsToDelete = $existingVariants->pluck('id')->diff($submittedVariantIds);
            if ($variantsToDelete->isNotEmpty()) {
                $product->variants()->whereIn('id', $variantsToDelete->all())->delete();
            }

            // 5. Đảm bảo luôn có một biến thể mặc định
            $product->refresh(); // Tải lại quan hệ variants sau khi xóa
            if ($product->variants()->count() > 0 && $product->variants()->where('is_default', true)->doesntExist()) {
                $firstVariant = $product->variants()->first();
                if ($firstVariant) {
                    $firstVariant->update(['is_default' => true]);
                }
            }
        }
}