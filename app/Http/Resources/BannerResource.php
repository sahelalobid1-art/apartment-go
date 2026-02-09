<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image_url' => $this->getImageUrl($this->image_url),
            'action_url' => $this->link,
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
