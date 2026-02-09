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
            ->with(['images', 'amenities'])
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
            $amenitiesIds = $data['amenities'] ?? [];
            unset($data['amenities']);

            $data['owner_id'] = Auth::id();

            $apartment = Apartment::create($data);

            if (!empty($amenitiesIds)) {
                $amenitiesIds = Amenity::whereIn('name', $amenitiesIds)
                    ->pluck('id')
                    ->toArray();
                $apartment->amenities()->sync($amenitiesIds);
            }

            if ($images && count($images) > 0) {
                $this->uploadImages($apartment, $images);
            }

            return $apartment->load('amenities');
        });
    }

    public function updateApartment(Apartment $apartment, array $data, ?array $newImages = null): Apartment
    {
        return DB::transaction(function () use ($apartment, $data, $newImages) {
            if (isset($data['amenities'])) {
                $amenitiesIds = $data['amenities'];
                    $amenitiesIds = Amenity::whereIn('name', $amenitiesIds)
                    ->pluck('id')
                    ->toArray();
                $apartment->amenities()->sync($amenitiesIds);
                unset($data['amenities']);
            }

            $apartment->update($data);

            if ($newImages && count($newImages) > 0) {
                $this->uploadImages($apartment, $newImages);
            }

            return $apartment->refresh()->load(['images', 'owner', 'amenities']);
        });
    }

    public function deleteApartment(Apartment $apartment): void
    {
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
