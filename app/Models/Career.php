<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;

class Career extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'image_id',
        'salary',
        'quantity',
        'education',
        'location',
        'type',
        'deadline',
        'description',
        'requirement',
        'benefit',
        'status',
        'is_home',
        'position',
    ];

    protected $casts = [
        'deadline' => 'date',
        'status'   => 'boolean',
        'is_home'  => 'boolean',
    ];

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}