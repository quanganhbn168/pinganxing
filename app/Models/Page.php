<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;


class Page extends Model
{
    use HasFactory, HasImages;

    protected $fillable = [
        'name ',
        'slug ',
        'title',
        'image',
        'banner',
        'description',
        'features',
        'content',
    ];
    protected $casts = [
        'features' => 'array',
        'content'  => 'array',
    ];
    
    public function details()
    {
        return $this->hasMany(PageDetail::class, 'page_id');
    }
}
