<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasFaqs;
use App\Traits\HasSlug;

class FieldCategory extends Model
{
    use HasFactory, HasSlug, HasFaqs, \App\Traits\HasCategoryTree;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'content',
        'solution_overview',
        'business_challenges',
        'cnetpos_solutions',
        'key_features',
        'impact_stats',
        'implementation_steps',
        'status',
        'is_home',
        'position',
        'order',
        'image_id',
        'banner_id',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'status' => 'boolean',
        'is_home' => 'boolean',
        'business_challenges' => 'array',
        'cnetpos_solutions' => 'array',
        'key_features' => 'array',
        'impact_stats' => 'array',
        'implementation_steps' => 'array',
    ];

    // ─── Relationships riêng ───

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    // ─── Scopes ───

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('order', 'asc');
    }
}
