<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    /**
     * جلب قائمة المحادثات (آخر رسالة مع كل مستخدم)
     */
    public function getUserConversations(int $userId): Collection
    {
        // تم نقل المنطق المعقد من الكونترولر إلى هنا
        return Message::where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function ($message) use ($userId) {
            return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
        })
        ->map(function ($messages) use ($userId) {
            $lastMessage = $messages->first();
            $otherUser = $lastMessage->sender_id === $userId ? $lastMessage->receiver : $lastMessage->sender;

            // حساب الرسائل غير المقروءة الواردة من الطرف الآخر
            $unreadCount = $messages->where('receiver_id', $userId)
                                    ->where('is_read', false)
                                    ->count();

            return [
                'user' => $otherUser,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
            ];
        })
        ->values();
    }

    /**
     * جلب سجل المحادثة بين المستخدم الحالي ومستخدم آخر وتحديدها كمقروءة
     */
    public function getConversationHistory(int $currentUserId, int $otherUserId)
    {
        $messages = Message::where(function ($query) use ($currentUserId, $otherUserId) {
            $query->where('sender_id', $currentUserId)
                  ->where('receiver_id', $otherUserId);
        })->orWhere(function ($query) use ($currentUserId, $otherUserId) {
            $query->where('sender_id', $otherUserId)
                  ->where('receiver_id', $currentUserId);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // تحديد الرسائل الواردة كمقروءة
        Message::where('sender_id', $otherUserId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $messages;
    }

    /**
     * إرسال رسالة جديدة
     */
    public function sendMessage(array $data): Message
    {
        $data['sender_id'] = Auth::id();

        $message = Message::create($data);

        // هنا يمكنك إضافة كود إرسال الـ Push Notification
        // $this->notificationService->sendPush(...)

        return $message;
    }

    /**
     * تحديد رسالة محددة كمقروءة
     */
    public function markMessageAsRead(Message $message): void
    {
        $message->update(['is_read' => true]);
    }
}
