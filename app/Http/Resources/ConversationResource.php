<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => $this['user'],
            'last_message' => new MessageResource($this['last_message']),
            'unread_count' => $this['unread_count'],
        ];
    }
}
