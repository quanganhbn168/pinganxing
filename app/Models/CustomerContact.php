<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $fillable = ['customer_id', 'type', 'value', 'label', 'is_primary'];
}