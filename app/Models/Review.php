<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'apartment_id',
        'tenant_id',
        'rating',
        'comment',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    protected static function booted()
    {
        static::created(function ($review) {
            $review->apartment->updateRating();
        });

        static::updated(function ($review) {
            $review->apartment->updateRating();
        });

        static::deleted(function ($review) {
            $review->apartment->updateRating();
        });
    }
}
