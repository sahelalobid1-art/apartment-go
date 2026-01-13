<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteService
{
    /**
     * جلب قائمة المفضلة للمستخدم مع الصور
     */
    public function getUserFavorites(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $user->favorites()
            ->with('images') // تأكد أن علاقة images موجودة في موديل Apartment
            ->paginate($perPage);
    }

    /**
     * التبديل بين الإضافة والحذف من المفضلة
     * تعيد مصفوفة تحتوي على الحالة الجديدة والرسالة المناسبة
     */
    public function toggleFavorite(User $user, int $apartmentId): array
    {
        // دالة toggle في لارافيل تقوم بالإضافة إذا لم يكن موجوداً، والحذف إذا كان موجوداً
        // وتعيد مصفوفة تحوي الـ IDs التي تم إضافتها أو حذفها
        $changes = $user->favorites()->toggle($apartmentId);

        // إذا كانت مصفوفة 'attached' تحتوي بيانات، فهذا يعني أنه تمت الإضافة
        $isFavorite = !empty($changes['attached']);

        return [
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Added to favorites' : 'Removed from favorites',
        ];
    }

    /**
     * التحقق مما إذا كانت الشقة في المفضلة
     */
    public function isFavorite(User $user, int $apartmentId): bool
    {
        return $user->favorites()
            ->where('apartment_id', $apartmentId)
            ->exists();
    }
}
