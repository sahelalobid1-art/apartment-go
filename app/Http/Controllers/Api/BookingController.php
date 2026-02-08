<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\RejectBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $bookings = $this->bookingService->getTenantBookings($request->user()->id);
        return BookingResource::collection($bookings);
    }

    public function show($id)
    {
        $booking = Booking::with(['apartment.images', 'apartment.owner', 'tenant'])->findOrFail($id);

        Gate::authorize('view', $booking);

        return new BookingResource($booking);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->createBooking($request->validated());

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => new BookingResource($booking->load('apartment')),
        ], 201);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        Gate::authorize('update', $booking);

        $booking = $this->bookingService->updateBooking($booking, $request->validated());

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => new BookingResource($booking),
        ]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        Gate::authorize('delete', $booking);

        $this->bookingService->cancelBooking($booking);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    public function approve(Booking $booking): JsonResponse
    {
        Gate::authorize('manage', $booking);

        $booking = $this->bookingService->approveBooking($booking);

        return response()->json([
            'message' => 'Booking approved successfully',
            'booking' => new BookingResource($booking),
        ]);
    }

    public function reject(RejectBookingRequest $request, Booking $booking): JsonResponse
    {
        Gate::authorize('manage', $booking);

        $booking = $this->bookingService->rejectBooking($booking, $request->reason);

        return response()->json([
            'message' => 'Booking rejected successfully',
            'booking' => new BookingResource($booking),
        ]);
    }

    public function ownerBookings(Request $request)
    {
        $bookings = $this->bookingService->getOwnerBookings($request->user()->id);
        return BookingResource::collection($bookings);
    }
}
