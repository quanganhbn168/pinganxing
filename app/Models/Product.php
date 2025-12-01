<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasImages;
use App\Traits\HasSlug;

class Product extends Model
{
    use HasFactory, HasImages, HasSlug;

    protected $fillable = [
        'type',
        'category_id',
        'brand_id',
        'name',
        'code',
        'slug',
        'image',
        'description',
        'content',
        'specifications',
        'price',
        'price_discount',
        'stock',
        'status',
        'has_variants',
        'is_on_sale',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_image',
        'meta_keywords',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_featured' => 'boolean',
        'has_variants' => 'boolean',
    ];
    const TYPE_PHYSICS         = 'physics';
    const TYPE_SERVICE        = 'services';


    // -- Các mối quan hệ (Relationships) --
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Quan hệ cho các Biến thể (SKUs).
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Quan hệ cho các Thuộc tính Lọc/Thông số kỹ thuật.
     */
    public function specifications(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_product');
    }

    /**
     * Accessor để tính toán và lấy phần trăm giảm giá.
     * Tự động tạo ra thuộc tính "ảo" $product->discount_percent
     *
     * @return int
     */
    public function getDiscountPercentAttribute(): int
    {
        // Kiểm tra để tránh lỗi chia cho 0 nếu giá gốc bằng 0
        if ($this->price <= 0 || $this->price_discount <= 0 || $this->price_discount >= $this->price) {
            return 0;
        }

        // Công thức: ((giá gốc - giá giảm) / giá gốc) * 100
        $discount = (($this->price - $this->price_discount) / $this->price) * 100;

        // Làm tròn số để có một con số nguyên đẹp (ví dụ: 15% thay vì 15.245%)
        return round($discount);
    }

    public function variantsWithValues()
    {
        $this->loadMissing('variants.attributeValues');

        return $this->variants->map(function ($variant) {
            return [
                'id'               => $variant->id,
                'sku'              => $variant->sku,
                'price'            => (float) $variant->price,
                'compare_at_price' => (float) $variant->compare_at_price,
                'stock'            => (int) $variant->stock,
                'is_default'       => (bool) $variant->is_default,
                'values'           => $variant->attributeValues->pluck('id')->toArray(),
            ];
        });
    }
}