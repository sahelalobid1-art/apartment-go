<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;

class NotificationService
{
    /**
     * إرسال إشعار متعلق بالحجز (الدالة التي كانت ناقصة)
     */
    public function sendBookingNotification(User $recipient, Booking $booking, string $type)
    {
        // هنا يمكنك تحديد محتوى الإشعار بناءً على نوعه
        $data = [
            'booking_id' => $booking->id,
            'type' => $type, // new, updated, cancelled, approved, rejected
            'message' => $this->getNotificationMessage($type, $booking),
        ];

        // إنشاء الإشعار في قاعدة البيانات
        // يمكنك هنا أيضاً إرسال إيميل أو إشعار حقيقي (Push Notification)
        return Notification::create([
            'user_id' => $recipient->id,
            'type' => 'booking_' . $type,
            'data' => json_encode($data), // أو $data مباشرة إذا كان الحقل في الموديل casted to array
            'is_read' => false,
        ]);
    }

    /**
     * دالة مساعدة لتوليد نص الرسالة
     */
    private function getNotificationMessage(string $type, Booking $booking): string
    {
        return match ($type) {
            'new' => "New booking request for apartment: {$booking->apartment->title}",
            'updated' => "Booking updated for apartment: {$booking->apartment->title}",
            'cancelled' => "Booking cancelled for apartment: {$booking->apartment->title}",
            'approved' => "Your booking for {$booking->apartment->title} has been approved!",
            'rejected' => "Your booking for {$booking->apartment->title} was rejected.",
            default => "Update regarding your booking.",
        };
    }

    // --- الدوال السابقة الخاصة بالـ NotificationController ---

    public function getUserNotifications(int $userId, int $perPage = 20)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->update(['is_read' => true]);
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteNotification(Notification $notification): void
    {
        $notification->delete();
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
