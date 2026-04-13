<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_id',
        'issued_by',
        'issued_at',
        'expired_at',
        'description',
        'status',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'issued_at'  => 'date',
        'expired_at' => 'date',
    ];

    public function image()
    {
        return $this->belongsTo(Media::class, 'image_id');
    }
}
