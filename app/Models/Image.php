<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'imageable_type', 'imageable_id', 'role',
        'dir', 'main_path', 'variants', 'original_path', 'disk',
        'filename', 'ext', 'mime', 'size', 'width', 'height',
        'position', 'alt', 'title', 'custom',
    ];

    protected $casts = [
        'variants' => 'array',
        'custom'   => 'array',
        'size'     => 'integer',
        'width'    => 'integer',
        'height'   => 'integer',
        'position' => 'integer',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    // URL tiện dụng
    public function url(?string $variant = null): string
    {
        $path = $variant ? ($this->variants[$variant] ?? null) : $this->main_path;
        return $path ? \Storage::disk($this->disk)->url($path) : '';
    }
}
