<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->tenant_id || $user->id === $booking->apartment->owner_id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->tenant_id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->id === $booking->tenant_id;
    }

    public function manage(User $user, Booking $booking): bool
    {
        return $user->id === $booking->apartment->owner_id;
    }
}
