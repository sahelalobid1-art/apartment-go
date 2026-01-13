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
            // دالة للحصول على الرابط الكامل سواء كان في assets أو storage
            'image_url' => $this->getImageUrl($this->image_url),
            'action_url' => $this->link,
        ];
    }

    private function getImageUrl($path)
    {
        if (!$path) return null;
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;

        // إذا كانت الصورة مرفوعة (تبدأ بـ public) نحولها لـ storage
        if (str_starts_with($path, 'public/')) {
            $path = str_replace('public/', '', $path);
            return asset('storage/' . $path);
        }

        // إذا كانت في المجلد العام مباشرة (مثل assets/...)
        return asset($path);
    }
}
