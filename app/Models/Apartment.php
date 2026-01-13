<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'governorate',
        'city',
        'address',
        'price_per_night',
        'bedrooms',
        'bathrooms',
        'area',
        'max_guests',
        // 'amenities', // <--- تم حذفه لأنه أصبح في جدول منفصل
        'status',
        'average_rating',
        'total_reviews',
    ];

    protected $casts = [
        // 'amenities' => 'array', // <--- تم حذفه
        'price_per_night' => 'decimal:2',
        'area' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    // --- العلاقات ---

    // العلاقة الجديدة الاحترافية
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'apartment_amenity');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images()
    {
        return $this->hasMany(ApartmentImage::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // --- Scopes ---

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['governorate'])) {
            $query->where('governorate', $filters['governorate']);
        }
        if (isset($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        if (isset($filters['min_price'])) {
            $query->where('price_per_night', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price_per_night', '<=', $filters['max_price']);
        }
        if (isset($filters['bedrooms'])) {
            $query->where('bedrooms', '>=', $filters['bedrooms']);
        }
        // يمكن الآن إضافة فلترة حسب المرافق بسهولة (مثال إضافي)
        // if (isset($filters['amenities'])) {
        //    $query->whereHas('amenities', function($q) use ($filters) {
        //        $q->whereIn('id', $filters['amenities']);
        //    });
        // }

        return $query;
    }

    public function updateRating()
    {
        $this->average_rating = $this->reviews()->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }
}
