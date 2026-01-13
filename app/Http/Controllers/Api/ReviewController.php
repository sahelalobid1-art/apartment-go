<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        // تم نقل منطق التحقق من الحجز وحالته إلى السيرفس
        $review = $this->reviewService->createReview($request->validated());

        return response()->json([
            'message' => 'Review created successfully',
            'review' => new ReviewResource($review),
        ], 201);
    }

    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        // التحقق من الصلاحية (هل المستخدم هو كاتب التقييم؟)
        Gate::authorize('update', $review);

        $updatedReview = $this->reviewService->updateReview($review, $request->validated());

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => new ReviewResource($updatedReview),
        ]);
    }

    public function destroy(Review $review): JsonResponse
    {
        Gate::authorize('delete', $review);

        $this->reviewService->deleteReview($review);

        return response()->json(['message' => 'Review deleted successfully']);
    }

    public function apartmentReviews($apartmentId)
    {
        $reviews = $this->reviewService->getApartmentReviews($apartmentId);

        return ReviewResource::collection($reviews);
    }
}
