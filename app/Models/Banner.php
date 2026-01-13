<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'image_url', 'link', 'is_active', 'order'];

    // Scope لجلب الفعال فقط
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
