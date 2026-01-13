<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favorite\ToggleFavoriteRequest;
use App\Http\Resources\ApartmentResource; // نستخدم الريسورس الذي أنشأناه سابقاً
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected FavoriteService $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
        // $this->middleware('auth:api'); // يفضل وضعه في ملف routes/api.php
    }

    public function index(Request $request)
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user());

        // استخدام ApartmentResource لتوحيد شكل البيانات مع باقي التطبيق
        return ApartmentResource::collection($favorites);
    }

    public function toggle(ToggleFavoriteRequest $request): JsonResponse
    {
        $result = $this->favoriteService->toggleFavorite(
            $request->user(),
            $request->apartment_id
        );

        return response()->json($result);
    }

    public function check(Request $request, $apartmentId): JsonResponse
    {
        $isFavorite = $this->favoriteService->isFavorite($request->user(), $apartmentId);

        return response()->json(['is_favorite' => $isFavorite]);
    }
}
