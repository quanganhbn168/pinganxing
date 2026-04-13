<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_at_price',
        'stock',
        'image_id',
        'is_default',
        'options',
    ];

    protected $casts = [
        'options'    => 'array',
        'is_default' => 'boolean',
    ];

    // ─── Accessors ───

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_at_price > $this->price;
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->getIsOnSaleAttribute()) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
        }
        return 0;
    }

    // ─── Relationships ───

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}