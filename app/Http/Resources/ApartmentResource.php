<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AmenityResource;
use App\Http\Resources\UserResource;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // معالجة الصور
        $imageUrls = [];
        if ($this->relationLoaded('images') && $this->images) {
            foreach ($this->images as $img) {
                if (!empty($img->image_url)) {
                    $imageUrls[] = $this->getImageUrl($img->image_url);
                }
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title ?? '',
            'description' => $this->description ?? '',
            'governorate' => $this->governorate ?? '',
            'city' => $this->city ?? '',
            'address' => $this->address ?? '',
            'price_per_night' => (float) ($this->price_per_night ?? 0),
            'bedrooms' => (int) ($this->bedrooms ?? 0),
            'bathrooms' => (int) ($this->bathrooms ?? 0),
            'area' => (float) ($this->area ?? 0),
            'max_guests' => (int) ($this->max_guests ?? 0),
            'rating' => (float) ($this->average_rating ?? 0),
            'status' => $this->status ?? 'available',
            'is_favorite' => false,

            'images' => $imageUrls,

            'owner' => new UserResource($this->whenLoaded('owner')),

            // هنا السحر! نستخدم Resource Collection للعلاقة مباشرة
            'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),

            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : now()->toIso8601String(),
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
