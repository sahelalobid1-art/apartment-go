<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // البيانات هنا تأتي من مصفوفة قمنا بتشكيلها في السيرفس
        return [
            'user' => $this['user'], // بيانات المستخدم الآخر
            'last_message' => new MessageResource($this['last_message']),
            'unread_count' => $this['unread_count'],
        ];
    }
}
