<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;
use App\Traits\HasSlug;

class Intro extends Model
{
    /** @use HasFactory<\Database\Factories\IntroFactory> */
    use HasFactory, HasImages, HasSlug;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function slug()
    {
        return $this->morphOne(\App\Models\Slug::class, 'sluggable');
    }
    public static function getSubMenuItems()
    {
    
        $allItems = self::where('status', 1)
        ->orderBy('id', 'asc')
        ->get();

    
        return $allItems->slice(1);
    }
}
