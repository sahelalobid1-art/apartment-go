<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'check_in' => $this->check_in_date,
            'check_out' => $this->check_out_date,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'cancellation_reason' => $this->when($this->status === 'rejected', $this->cancellation_reason),
            // تحميل العلاقات فقط إذا كانت متوفرة لتجنب N+1 query
            'apartment' => new ApartmentResource($this->whenLoaded('apartment')),
            'tenant' => $this->whenLoaded('tenant'),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
