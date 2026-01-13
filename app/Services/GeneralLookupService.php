<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Amenity;
use App\Models\Governorate;
use Illuminate\Support\Collection;

class GeneralLookupService
{
    /**
     * جلب البنرات النشطة للصفحة الرئيسية
     */
    public function getActiveBanners(): Collection
    {
        // يمكن إضافة كاش هنا لتحسين الأداء
        return Banner::active()->get(['id', 'title', 'image_url', 'link']);
    }

    /**
     * جلب قائمة المحافظات
     */
    public function getAllGovernorates(): Collection
    {
        return Governorate::all(['id', 'name', 'image_url']);
    }

    /**
     * جلب قائمة المرافق
     */
    public function getAllAmenities(): Collection
    {
        return Amenity::all(['id', 'name', 'icon']);
    }
}
