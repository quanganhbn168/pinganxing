<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ─── Scopes ───

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ─── Relationships ───

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    // ─── Helpers ───

    public function getTextColorAttribute(): string
    {
        $hex = ltrim($this->color, '#');
        $r = hexdec(substr($hex, 0, 2)) ?: 0;
        $g = hexdec(substr($hex, 2, 2)) ?: 0;
        $b = hexdec(substr($hex, 4, 2)) ?: 0;

        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    public function getBadgeHtmlAttribute(): string
    {
        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s; padding: 0.25rem 0.5rem; border-radius: 999px;">%s</span>',
            $this->color,
            $this->text_color,
            e($this->name)
        );
    }
}
