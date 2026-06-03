<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImportRow extends Model
{
    use HasFactory;

    public const STATUS_RAW = 'raw';
    public const STATUS_READY = 'ready';
    public const STATUS_NEEDS_REVIEW = 'needs_review';
    public const STATUS_SKIPPED = 'skipped';
    public const STATUS_IMPORTED = 'imported';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'product_import_batch_id',
        'product_import_sheet_id',
        'row_number',
        'raw_cells',
        'normalized',
        'code',
        'name',
        'description',
        'content',
        'specifications',
        'price',
        'category_id',
        'suggested_category_id',
        'category_path',
        'image_id',
        'gallery',
        'product_id',
        'status',
        'warnings',
        'errors',
    ];

    protected $casts = [
        'raw_cells' => 'array',
        'normalized' => 'array',
        'gallery' => 'array',
        'warnings' => 'array',
        'errors' => 'array',
        'price' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductImportBatch::class, 'product_import_batch_id');
    }

    public function sheet(): BelongsTo
    {
        return $this->belongsTo(ProductImportSheet::class, 'product_import_sheet_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suggestedCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'suggested_category_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ProductImportAsset::class);
    }
}
