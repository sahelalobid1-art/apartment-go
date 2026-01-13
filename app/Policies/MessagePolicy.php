<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * السماح بتحديد الرسالة كمقروءة فقط إذا كان المستخدم هو المستقبل
     */
    public function markAsRead(User $user, Message $message): bool
    {
        return $user->id === $message->receiver_id;
    }

    /**
     * السماح برؤية الرسالة (اختياري)
     */
    public function view(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }
}
