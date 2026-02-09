<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'apartment_id' => $this->apartment_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at->toIso8601String(),
            'tenant' => $this->whenLoaded('tenant', function () {
                return [
                    'id' => $this->tenant->id,
                    'name' => $this->tenant->name,
                ];
            }),
        ];
    }
}
