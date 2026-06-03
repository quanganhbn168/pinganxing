<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImportBatch extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_EXTRACTING = 'extracting';
    public const STATUS_EXTRACTED = 'extracted';
    public const STATUS_NORMALIZING = 'normalizing';
    public const STATUS_READY = 'ready';
    public const STATUS_COMMITTING = 'committing';
    public const STATUS_COMMITTED = 'committed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'product_import_profile_id',
        'original_filename',
        'disk',
        'stored_path',
        'source_hash',
        'status',
        'total_sheets',
        'total_rows',
        'ready_rows',
        'review_rows',
        'imported_rows',
        'failed_rows',
        'skipped_rows',
        'assets_count',
        'started_at',
        'finished_at',
        'errors',
        'meta',
    ];

    protected $casts = [
        'errors' => 'array',
        'meta' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ProductImportProfile::class, 'product_import_profile_id');
    }

    public function sheets(): HasMany
    {
        return $this->hasMany(ProductImportSheet::class);
    }

    public function rows(): HasMany
    {
        return $this->hasMany(ProductImportRow::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ProductImportAsset::class);
    }
}
