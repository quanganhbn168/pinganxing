<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;

class Intro extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'description',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public static function getSubMenuItems()
    {
        return self::where('status', 1)
            ->orderBy('id', 'asc')
            ->get()
            ->slice(1);
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner()
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }
}
