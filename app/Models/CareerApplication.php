<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareerApplication extends Model
{
    protected $fillable = [
        'career_id', 'name', 'email', 'phone', 'cv_path', 'message', 'status'
    ];

    public function career()
    {
        return $this->belongsTo(Career::class);
    }
}