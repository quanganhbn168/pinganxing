<?php

// app/Models/ServiceCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;
use App\Traits\HasSlug;
class ServiceCategory extends Model
{
    use HasFactory, HasSlug, HasImages;
    protected $table = 'service_categories';
    protected $fillable = [
        'name','slug', 'image', 'banner', 'parent_id', 'status','is_home','is_menu','is_footer', 'description', 'content'
    ];

    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    
    public function slug()
    {
        return $this->morphOne(Slug::class, 'sluggable');
    }

    public function getSlugUrlAttribute()
    {
        return url($this->slug->slug ?? '#');
    }
}
