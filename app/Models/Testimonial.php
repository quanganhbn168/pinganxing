<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;
class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory, HasImages;
    protected $fillable = [
        'name',
        'position',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
