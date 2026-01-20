<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;

class HomepageSection extends Model
{
    use HasFactory, HasImages;

    protected $fillable = [
        'key',
        'name',
        'title',
        'subtitle',
        'description',
        'image',
        'background_image',
        'settings',
        'is_active',
        'order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Lấy sections đang active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Sắp xếp theo order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Helper: Lấy giá trị từ settings JSON
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Helper: Cập nhật 1 giá trị trong settings
     */
    public function setSetting(string $key, $value): self
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        return $this;
    }

    /**
     * Get image URL (for consistency with other models)
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Get background image URL
     */
    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image ? asset('storage/' . $this->background_image) : null;
    }
}
