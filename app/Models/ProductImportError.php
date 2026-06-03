<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImportError extends Model
{
    protected $fillable = [
        'session_id',
        'brand',
        'code',
        'name',
        'error_type',
        'error_message',
        'raw_product',
    ];

    protected $casts = [
        'raw_product' => 'array',
    ];
}