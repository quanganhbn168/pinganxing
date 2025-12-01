<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasImages;
class Brand extends Model
{
    use HasFactory, HasImages;

    protected $fillable = ['name', 'slug', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class); 
    }
}
