<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['name', 'image_url'];

    // علاقة لجلب الشقق التابعة لهذه المحافظة (اختياري)
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'governorate', 'name');
    }
}
