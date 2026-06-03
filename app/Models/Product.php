<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasSlug;
use App\Traits\HasSeo;
use App\Traits\HasFaqs;
use App\Traits\HasTags;

class Product extends Model
{
    use HasFactory, HasSlug, HasSeo, HasFaqs, HasTags;

    protected $fillable = [
        'type',
        'category_id',
        'brand_id',
        'name',
        'code',
        'image_id',
        'gallery',
        'banner_id',
        'description',
        'content',
        'specifications',
        'price',
        'price_discount',
        'stock',
        'status',
        'has_variants',
        'is_featured',
        'is_home',
        'is_on_sale',
        'discount_type',
        'discount_value',
        'product_type',
        'meta_title',
        'meta_description',
        'meta_image_id',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery' => 'array',
        'price' => 'decimal:2',
        'price_discount' => 'decimal:2',
        'status' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_featured' => 'boolean',
        'is_home' => 'boolean',
        'has_variants' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    const TYPE_PHYSICS  = 'physical';
    const TYPE_SERVICE  = 'services';

    // ─── Relationships ───

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    // ─── Accessors ───

    public function getDiscountPercentAttribute(): int
    {
        if ($this->price <= 0 || $this->price_discount <= 0 || $this->price_discount >= $this->price) {
            return 0;
        }
        return round((($this->price - $this->price_discount) / $this->price) * 100);
    }
}
