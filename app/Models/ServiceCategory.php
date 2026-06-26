<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasSlug;

class ServiceCategory extends Model
{
    use HasFactory, HasSlug, \App\Traits\HasCategoryTree;

    protected $table = 'service_categories';

    protected $fillable = [
        'name',
        'image_id',
        'banner_id',
        'parent_id',
        'position',
        'status',
        'is_home',
        'is_menu',
        'is_footer',
        'description',
        'content',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'status' => 'boolean',
        'is_home' => 'boolean',
        'is_menu' => 'boolean',
        'is_footer' => 'boolean',
    ];

    // ─── Relationships riêng ───

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
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
