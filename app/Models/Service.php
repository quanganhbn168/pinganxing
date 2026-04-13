<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;
use App\Traits\HasComments;

class Service extends Model
{
    use HasFactory, HasSlug, HasComments;

    protected $fillable = [
        'service_category_id',
        'name',
        'image_id',
        'banner_id',
        'description',
        'content',
        'is_home',
        'status',
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

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
