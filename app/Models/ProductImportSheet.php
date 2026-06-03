<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImportSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_import_batch_id',
        'sheet_index',
        'name',
        'highest_row',
        'highest_column',
        'status',
        'headings',
        'meta',
    ];

    protected $casts = [
        'headings' => 'array',
        'meta' => 'array',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductImportBatch::class, 'product_import_batch_id');
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
