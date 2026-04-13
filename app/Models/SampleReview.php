<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleReview extends Model
{
    protected $fillable = [
        'rating',
        'content',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
