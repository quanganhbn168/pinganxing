<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_id',
        'link',
        'status',
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
