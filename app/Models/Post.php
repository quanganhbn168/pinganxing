<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;
use App\Traits\HasComments;

class Post extends Model
{
    use HasFactory, HasSlug, HasComments;

    protected $fillable = [
        'post_category_id',
        'title',
        'image_id',
        'banner_id',
        'description',
        'content',
        'is_featured',
        'status',
        'is_home',
    ];

    protected $casts = [
        'gallery'     => 'array',
        'is_featured' => 'boolean',
        'status'      => 'boolean',
        'is_home'     => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner()
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
