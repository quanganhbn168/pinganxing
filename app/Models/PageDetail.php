<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;


class PageDetail extends Model
{
    use HasFactory, HasImages;

    protected $fillable = [
        'page_id',
        'content',
    ];

    public function parent()
    {
        return $this->belongsTo(Page::class);
    }
}
