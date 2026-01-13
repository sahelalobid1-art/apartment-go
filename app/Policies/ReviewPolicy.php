<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * السماح بالتعديل فقط لصاحب التقييم
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->tenant_id;
    }

    /**
     * السماح بالحذف فقط لصاحب التقييم
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->tenant_id;
    }
}
