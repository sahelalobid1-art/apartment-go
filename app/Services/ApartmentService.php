<?php

namespace App\Services;

use App\Models\Amenity;
use App\Models\Apartment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class ApartmentService
{
    public function getAllApartments(array $filters)
    {
        // الآن يمكننا استخدام with('amenities') بشكل نظامي وصحيح
        $query = Apartment::with(['images', 'owner', 'amenities'])
            ->available();

        if (isset($filters) && count($filters) > 0) {
             $query->filter($filters);
        }

        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('city', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function getOwnerApartments(int $ownerId)
    {
        return Apartment::where('owner_id', $ownerId)
            ->with(['images', 'amenities']) // تحميل المرافق
            ->latest()
            ->paginate(10);
    }

    public function getApartmentById($id)
    {
        return Apartment::with(['images', 'owner', 'amenities', 'reviews.tenant'])
            ->findOrFail($id);
    }

    public function createApartment(array $data, ?array $images): Apartment
    {
        return DB::transaction(function () use ($data, $images) {
            // 1. استخراج مصفوفة المرافق من البيانات (لأنها لم تعد موجودة في جدول الشقق)
            $amenitiesIds = $data['amenities'] ?? [];
            unset($data['amenities']); // حذفها من المصفوفة حتى لا تسبب خطأ عند الإنشاء

            $data['owner_id'] = Auth::id();

            // 2. إنشاء الشقة
            $apartment = Apartment::create($data);

            // 3. ربط المرافق بالجدول الوسيط
            if (!empty($amenitiesIds)) {
                // sync تقوم بربط الـ IDs وتتأكد من عدم التكرار
                $amenitiesIds = Amenity::whereIn('name', $amenitiesIds)
                    ->pluck('id')
                    ->toArray();
                $apartment->amenities()->sync($amenitiesIds);
            }

            // 4. رفع الصور
            if ($images && count($images) > 0) {
                $this->uploadImages($apartment, $images);
            }

            return $apartment->load('amenities'); // إعادة التحميل لتظهر في الرد
        });
    }

    public function updateApartment(Apartment $apartment, array $data, ?array $newImages = null): Apartment
    {
        return DB::transaction(function () use ($apartment, $data, $newImages) {
            // 1. التعامل مع المرافق
            if (isset($data['amenities'])) {
                $amenitiesIds = $data['amenities'];
                    $amenitiesIds = Amenity::whereIn('name', $amenitiesIds)
                    ->pluck('id')
                    ->toArray();
                $apartment->amenities()->sync($amenitiesIds); // تحديث العلاقات (حذف القديم وإضافة الجديد)
                unset($data['amenities']);
            }

            // 2. تحديث بيانات الشقة
            $apartment->update($data);

            // 3. رفع الصور الجديدة
            if ($newImages && count($newImages) > 0) {
                $this->uploadImages($apartment, $newImages);
            }

            return $apartment->refresh()->load(['images', 'owner', 'amenities']);
        });
    }

    public function deleteApartment(Apartment $apartment): void
    {
        // العلاقات في الجدول الوسيط ستحذف تلقائياً بسبب onDelete('cascade') في الميجريشن
        $apartment->delete();
    }

    private function uploadImages(Apartment $apartment, array $images): void
    {
        foreach ($images as $index => $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('apartments', 'public');

                $apartment->images()->create([
                    'image_url' => $path,
                    'is_primary' => $index === 0 && $apartment->images()->count() === 0,
                ]);
            }
        }
    }
}
