<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductImportProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vendor',
        'description',
        'column_map',
        'options',
        'is_active',
    ];

    protected $casts = [
        'column_map' => 'array',
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(ProductImportBatch::class);
    }

    public function categoryMaps(): HasMany
    {
        return $this->hasMany(ProductImportCategoryMap::class);
    }
}
