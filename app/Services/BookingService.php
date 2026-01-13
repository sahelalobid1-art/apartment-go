<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Apartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BookingService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getTenantBookings(int $userId)
    {
        return Booking::with(['apartment.images', 'apartment.owner'])
            ->where('tenant_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getOwnerBookings(int $ownerId)
    {
        // جلب الشقق المملوكة للمستخدم أولاً
        $apartmentIds = Apartment::where('owner_id', $ownerId)->pluck('id');

        return Booking::with(['apartment.images', 'tenant'])
            ->whereIn('apartment_id', $apartmentIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function createBooking(array $data): Booking
    {
        $apartment = Apartment::findOrFail($data['apartment_id']);

        $this->ensureApartmentAvailable($apartment->id, $data['check_in_date'], $data['check_out_date']);

        $totalPrice = $this->calculatePrice($apartment->price_per_night, $data['check_in_date'], $data['check_out_date']);

        return DB::transaction(function () use ($data, $totalPrice, $apartment) {
            $booking = Booking::create([
                'apartment_id' => $data['apartment_id'],
                'tenant_id' => Auth::id(),
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'total_price' => $totalPrice,
                'payment_method' => $data['payment_method'],
                'payment_info' => $data['payment_info'],
                'status' => 'pending',
            ]);

            $this->notificationService->sendBookingNotification($apartment->owner, $booking, 'new');

            return $booking;
        });
    }

    public function updateBooking(Booking $booking, array $data): Booking
    {
        // التحقق من حالة الحجز
        if (!in_array($booking->status, ['pending', 'approved'])) {
            throw ValidationException::withMessages(['error' => 'Cannot update this booking']);
        }

        $checkIn = $data['check_in_date'] ?? $booking->check_in_date;
        $checkOut = $data['check_out_date'] ?? $booking->check_out_date;

        // التحقق من التوفر مع استثناء الحجز الحالي
        $this->ensureApartmentAvailable($booking->apartment_id, $checkIn, $checkOut, $booking->id);

        // إعادة حساب السعر إذا تغيرت التواريخ
        if (isset($data['check_in_date']) || isset($data['check_out_date'])) {
            $booking->total_price = $this->calculatePrice($booking->apartment->price_per_night, $checkIn, $checkOut);
            $booking->status = 'pending'; // إعادة الحالة للمراجعة
        }

        $booking->update($data);

        $this->notificationService->sendBookingNotification($booking->apartment->owner, $booking, 'updated');

        return $booking;
    }

    public function cancelBooking(Booking $booking): void
    {
        if ($booking->status === 'completed') {
            throw ValidationException::withMessages(['error' => 'Cannot cancel completed booking']);
        }

        $booking->update(['status' => 'cancelled']);

        $this->notificationService->sendBookingNotification($booking->apartment->owner, $booking, 'cancelled');
    }

    public function approveBooking(Booking $booking): Booking
    {
        if ($booking->status !== 'pending') {
            throw ValidationException::withMessages(['error' => 'Booking is not pending']);
        }

        $booking->update(['status' => 'approved']);

        $this->notificationService->sendBookingNotification($booking->tenant, $booking, 'approved');

        return $booking;
    }

    public function rejectBooking(Booking $booking, string $reason): Booking
    {
        if ($booking->status !== 'pending') {
            throw ValidationException::withMessages(['error' => 'Booking is not pending']);
        }

        $booking->update([
            'status' => 'rejected',
            'cancellation_reason' => $reason,
        ]);

        $this->notificationService->sendBookingNotification($booking->tenant, $booking, 'rejected');

        return $booking;
    }

    // دوال مساعدة خاصة بالسيرفس
    private function ensureApartmentAvailable($apartmentId, $checkIn, $checkOut, $excludeBookingId = null)
    {
        // نستخدم دالة الـ Model الأصلية إذا كانت موجودة، أو نكتب الكويري هنا
        if (Booking::checkAvailability($apartmentId, $checkIn, $checkOut, $excludeBookingId)) {
            throw ValidationException::withMessages(['error' => 'Apartment not available for selected dates']);
        }
    }

    private function calculatePrice($pricePerNight, $checkIn, $checkOut)
    {
        $days = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        return $days * $pricePerNight;
    }
}
