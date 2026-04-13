<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'position',
        'image_id',
        'hsk_level',
        'experience',
        'bio',
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
