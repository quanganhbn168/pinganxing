<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Traits\HasSlug;

class FieldCategory extends Model
{
    use HasFactory, HasSlug, \App\Traits\HasCategoryTree;

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
        'related_product_ids',
        'related_project_ids',
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
        'related_product_ids' => 'array',
        'related_project_ids' => 'array',
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

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function activeFaqs(): MorphMany
    {
        return $this->faqs()->active();
    }

    // ─── Scopes ───

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('order', 'asc');
    }
}
