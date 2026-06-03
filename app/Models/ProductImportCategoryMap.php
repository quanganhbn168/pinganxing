<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductImportCategoryMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_import_profile_id',
        'source_type',
        'source_value',
        'normalized_value',
        'category_id',
        'category_path',
        'auto_create',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'auto_create' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $map): void {
            $map->normalized_value = self::normalizeSource((string) $map->source_value);
        });
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ProductImportProfile::class, 'product_import_profile_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function normalizeSource(string $value): string
    {
        return Str::of($value)
            ->squish()
            ->lower()
            ->ascii()
            ->toString();
    }
}
