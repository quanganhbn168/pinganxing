<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasImages;
use App\Traits\HasSlug;
class ProjectCategory extends Model
{
    /** @use HasFactory<\Database\Factories\PostCategoryFactory> */
    use HasFactory, HasImages, HasSlug;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'content',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'position',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'is_home'    => 'boolean',
        'is_menu'    => 'boolean',
        'is_footer'  => 'boolean',
        'parent_id'  => 'integer',
        'position'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) . '-' . Str::random(5);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    
}
