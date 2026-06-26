<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasSlug;
use App\Traits\HasSeo;
use App\Traits\HasTags;

class Tour extends Model
{
    use HasFactory, HasSlug, HasSeo, HasTags;

    protected $fillable = [
        'tour_category_id',
        'name',
        'slug',
        'code',
        'image_id',
        'gallery',
        'banner_id',
        'description',
        'content',
        'duration',
        'transport',
        'departure',
        'features',
        'price',
        'price_discount',
        'rating',
        'review_count',
        'status',
        'is_featured',
        'is_hot',
        'is_sale',
        'is_home',
        'meta_title',
        'meta_description',
        'meta_image_id',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery' => 'array',
        'features' => 'array',
        'price' => 'decimal:2',
        'price_discount' => 'decimal:2',
        'status' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_featured' => 'boolean',
        'is_home' => 'boolean',
        'is_hot' => 'boolean',
        'is_sale' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    public function getSlugUrlAttribute()
    {
        $categorySlug = $this->category ? $this->category->slug_value : 'danh-muc';
        $slug = $this->slug_value;
        if (!$slug) return url('#');
        return route('frontend.tours.show', ['categorySlug' => $categorySlug, 'slug' => $slug]);
    }
}
