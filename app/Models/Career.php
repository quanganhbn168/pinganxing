<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;
use App\Traits\HasSlug;


class Career extends Model
{
    use HasFactory, HasImages, HasSlug;

    protected $fillable = [
        'name', 'slug', 'image', 'salary', 'quantity', 
        'education', 'location', 'type', 'deadline',
        'description', 'requirement', 'benefit',
        'status', 'is_home', 'position'
    ];

    protected $casts = [
        'deadline' => 'date',
        'status'   => 'boolean',
        'is_home'  => 'boolean',
    ];

    // NẾU BẠN DÙNG BẢNG SLUGS RIÊNG (Polymorphic)
    public function slug()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    // Helper lấy URL
    public function getUrlAttribute()
    {
        // Nếu dùng bảng slugs riêng
        return url($this->slug->slug ?? '#');
    }
}