<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSlug;
use App\Traits\HasImages;

class Service extends Model
{
    use HasFactory, HasSlug, HasImages;

    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'image',
        'banner',
        'description',
        'content',
        'is_home',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class,"service_category_id");
    }

}
