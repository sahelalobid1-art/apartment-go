<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteService
{
    /**

     */
    public function getUserFavorites(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $user->favorites()
            ->with('images')
            ->paginate($perPage);
    }

    public function toggleFavorite(User $user, int $apartmentId): array
    {
        $changes = $user->favorites()->toggle($apartmentId);

        $isFavorite = !empty($changes['attached']);

        return [
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Added to favorites' : 'Removed from favorites',
        ];
    }

    public function isFavorite(User $user, int $apartmentId): bool
    {
        return $user->favorites()
            ->where('apartment_id', $apartmentId)
            ->exists();
    }
}
