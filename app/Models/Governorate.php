<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['name', 'image_url'];

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'governorate', 'name');
    }
}
