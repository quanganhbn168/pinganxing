<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasImages;
use App\Traits\HasSlug;

class Project extends Model
{
    use HasFactory, HasImages, HasSlug;

    protected $fillable = [
        'name',
        'image',
        'slug',
        'project_category_id',
        'description',
        'content',
        'status',
        'is_home',
        'banner',
        'investor',
        'address',
        'year',
        'value'
    ];

    protected $casts = [
        'project_category_id' => 'integer',
        'status'             => 'boolean',
        'is_home'            => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($project) {
            if (empty($project->slug) && ! empty($project->name)) {
                $project->slug = Str::slug($project->name) . '-' . Str::random(5);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

}
