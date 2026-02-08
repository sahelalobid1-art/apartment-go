<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReviewService
{
    public function createReview(array $data): Review
    {
        $booking = Booking::findOrFail($data['booking_id']);

        if ($booking->tenant_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'completed') {
            abort(400, 'Can only review completed bookings');
        }

        if ($booking->review()->exists()) {
            abort(400, 'Booking already reviewed');
        }

        return Review::create([
            'booking_id' => $data['booking_id'],
            'apartment_id' => $booking->apartment_id,
            'tenant_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);
    }

    public function updateReview(Review $review, array $data): Review
    {
        $review->update($data);
        return $review;
    }

    public function deleteReview(Review $review): void
    {
        $review->delete();
    }

    public function getApartmentReviews(int $apartmentId)
    {
        return Review::with('tenant')
            ->where('apartment_id', $apartmentId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
