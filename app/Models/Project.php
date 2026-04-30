<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;
use App\Traits\HasComments;
use App\Traits\HasFaqs;
use App\Traits\HasTags;

class Project extends Model
{
    use HasFactory, HasSlug, HasComments, HasFaqs, HasTags;

    protected $fillable = [
        'name',
        'image_id',
        'gallery',
        'project_category_id',
        'description',
        'content',
        'status',
        'is_home',
        'banner_id',
        'investor',
        'address',
        'year',
        'value',
    ];

    protected $casts = [
        'gallery'              => 'array',
        'project_category_id'  => 'integer',
        'status'               => 'boolean',
        'is_home'              => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function banner()
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
