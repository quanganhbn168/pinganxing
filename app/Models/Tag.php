<?php

namespace App\Models;

use App\Enums\TagType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'color',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'type' => TagType::class,
    ];

    // ─── Relationships ───

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag');
    }

    // ─── Scopes ───

    public function scopeForProducts($query)
    {
        return $query->where('type', TagType::PRODUCT);
    }

    public function scopeForPosts($query)
    {
        return $query->where('type', TagType::POST);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ─── Helpers ───

    public function getTextColorAttribute(): string
    {
        $hex = ltrim($this->color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    public function getBadgeHtmlAttribute(): string
    {
        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s;">%s</span>',
            $this->color,
            $this->text_color,
            e($this->name)
        );
    }
}