<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImportAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_import_batch_id',
        'product_import_sheet_id',
        'product_import_row_id',
        'media_id',
        'sheet_name',
        'drawing_name',
        'picture_name',
        'row_number',
        'column_number',
        'coordinate',
        'disk',
        'storage_path',
        'filename',
        'ext',
        'mime',
        'hash',
        'size',
        'width',
        'height',
        'is_ignored',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_ignored' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductImportBatch::class, 'product_import_batch_id');
    }

    public function sheet(): BelongsTo
    {
        return $this->belongsTo(ProductImportSheet::class, 'product_import_sheet_id');
    }

    public function row(): BelongsTo
    {
        return $this->belongsTo(ProductImportRow::class, 'product_import_row_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
