<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}

