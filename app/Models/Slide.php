<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'link',
        'button_text',
        'button_text_2',
        'link_2',
        'image_id',
        'position',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Lấy ảnh đầy đủ path (nếu dùng asset)
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset($this->image) : asset('images/setting/no-image.png');
    }

    /**
     * Scope lọc slide đang bật
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function getBeforeImageAttribute()
    {
        $img = is_array($this->image) ? $this->image : json_decode($this->image, true);
        return $img['before'] ?? null;
    }

    public function getAfterImageAttribute()
    {
        $img = is_array($this->image) ? $this->image : json_decode($this->image, true);
        return $img['after'] ?? null;
    }


    public function image()
    {
        return $this->belongsTo(\Awcodes\Curator\Models\Media::class, 'image_id');
    }
}
