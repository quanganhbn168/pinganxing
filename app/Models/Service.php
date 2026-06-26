<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;
use App\Traits\HasComments;
use App\Traits\HasFaqs;

class Service extends Model
{
    use HasFactory, HasSlug, HasComments, HasFaqs;

    protected $fillable = [
        'service_category_id',
        'name',
        'image_id',
        'gallery',
        'banner_id',
        'description',
        'content',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'unit_id',
        'price',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image_id',
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner()
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

}
