<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasSlug;

class ProjectCategory extends Model
{
    use HasFactory, HasSlug, \App\Traits\HasCategoryTree;

    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'content',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'position',
        'meta_description',
        'meta_keywords',
        'image_id',
        'banner_id',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'is_home'   => 'boolean',
        'is_menu'   => 'boolean',
        'is_footer' => 'boolean',
        'parent_id' => 'integer',
        'position'  => 'integer',
    ];

    // ─── Relationships riêng ───

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_category_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }
}
