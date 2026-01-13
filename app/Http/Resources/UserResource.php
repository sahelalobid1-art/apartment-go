<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'full_name' => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')),
            'phone' => $this->phone ?? '',
            'user_type' => $this->user_type ?? 'user',
            'profile_image' => $this->getImageUrl($this->profile_image),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }

    private function getImageUrl($path)
    {
        if (!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;

        $path = str_replace('public/', '', $path);
        if (!str_starts_with($path, 'storage/')) {
            $path = 'storage/' . $path;
        }
        return asset($path);
    }
}
