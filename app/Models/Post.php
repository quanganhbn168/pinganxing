<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;
use App\Traits\HasComments;
use App\Traits\HasSeo;

class Post extends Model
{
    use HasFactory, HasSlug, HasComments, HasSeo;

    protected $fillable = [
        'post_category_id',
        'title',
        'image_id',
        'banner_id',
        'gallery',
        'description',
        'content',
        'is_featured',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'meta_description',
        'meta_keywords',
        'meta_image_id',
    ];

    protected $casts = [
        'gallery'     => 'array',
        'is_featured' => 'boolean',
        'status'      => 'boolean',
        'is_home'     => 'boolean',
        'is_menu'     => 'boolean',
        'is_footer'   => 'boolean',
    ];

    // ==========================================
    // RELATIONS
    // ==========================================

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    /** Ảnh đại diện (Curator) */
    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    /** Banner (Curator) */
    public function banner()
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    /** Ảnh SEO (Curator) */
    public function metaImage()
    {
        return $this->belongsTo(Media::class, 'meta_image_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    // ==========================================
    // IMAGE HELPERS (tương thích với View cũ dùng mainImage() / bannerImage())
    // ==========================================

    /**
     * Lấy Curator Media object của ảnh đại diện.
     * Tương thích với cú pháp optional($post->mainImage())->url() trong View.
     */
    public function mainImage(): ?Media
    {
        return $this->image;
    }

    /**
     * Lấy Curator Media object của banner.
     * Tương thích với cú pháp optional($post->bannerImage())->url() trong View.
     */
    public function bannerImage(): ?Media
    {
        return $this->banner;
    }

    /**
     * URL ảnh đại diện (shortcut).
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image?->url;
    }

    /**
     * URL banner (shortcut).
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner?->url;
    }

    // ==========================================
    // AGGREGATE RATING (SEO Schema.org)
    // ==========================================

    public function getAggregateRatingData(): ?array
    {
        $approved = $this->approvedComments ?? collect();
        if ($approved->isEmpty()) {
            return null;
        }

        $ratings = $approved->whereNotNull('rating')->pluck('rating');
        if ($ratings->isEmpty()) {
            return null;
        }

        return [
            '@type'       => 'AggregateRating',
            'ratingValue' => round($ratings->avg(), 1),
            'reviewCount' => $ratings->count(),
            'bestRating'  => 5,
            'worstRating' => 1,
        ];
    }
}
