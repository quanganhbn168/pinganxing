<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasSlug;

class FieldCategory extends Model
{
    use HasFactory, HasSlug, \App\Traits\HasCategoryTree;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'content',
        'status',
        'position',
    ];

    protected $casts = [
        'status' => 'boolean',
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

    // ─── Scopes ───

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('order', 'asc');
    }
}