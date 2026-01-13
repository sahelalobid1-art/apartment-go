<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_url' => $this->getImageUrl($this->image_url),
        ];
    }

    private function getImageUrl($path)
    {
        if (!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;

        if (str_starts_with($path, 'public/')) {
            $path = str_replace('public/', '', $path);
            return asset('storage/' . $path);
        }

        return asset($path);
    }
}
