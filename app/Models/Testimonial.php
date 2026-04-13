<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'position',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function image()
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'image_id');
    }

    public function banner()
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'banner_id');
    }

}
