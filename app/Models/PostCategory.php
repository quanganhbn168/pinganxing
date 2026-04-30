<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasSlug;

class PostCategory extends Model
{
    use HasFactory, HasSlug, \App\Traits\HasCategoryTree;

    protected $fillable = [
        'parent_id',
        'name',
        'image_id',
        'banner_id',
        'status',
        'is_home',
        'description',
        'content',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'is_home'   => 'boolean',
        'parent_id' => 'integer',
    ];

    // ─── Relationships riêng ───

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
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
