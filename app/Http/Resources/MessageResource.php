<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'booking_id' => $this->booking_id,
            'message' => $this->message,
            'is_read' => (bool) $this->is_read,
            'created_at' => $this->created_at->toIso8601String(),
            'sender' =>$this->sender,
            'receiver' => $this->sender,
        ];
    }
}
