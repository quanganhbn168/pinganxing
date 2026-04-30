<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasSlug;
use App\Traits\HasComments;
use App\Traits\HasFaqs;

class Field extends Model
{
    use HasFactory, HasSlug, HasComments, HasFaqs;

    protected $fillable = [
        'field_category_id',
        'name',
        'image_id',
        'gallery',
        'summary',
        'content',
        'status',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery'     => 'array',
        'status'      => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ─── Relationships ───

    public function category(): BelongsTo
    {
        return $this->belongsTo(FieldCategory::class, 'field_category_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    // ─── Scopes ───

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
