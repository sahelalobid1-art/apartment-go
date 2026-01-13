<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apartment\StoreApartmentRequest;
use App\Http\Requests\Apartment\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Services\ApartmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ApartmentController extends Controller
{
    protected ApartmentService $apartmentService;

    public function __construct(ApartmentService $apartmentService)
    {
        $this->apartmentService = $apartmentService;
    }

    public function index(Request $request)
    {
        // يجب أن تتأكد أن الـ Service يعيد البيانات مع العلاقات (Eager Loading)
        // أو يمكنك تحميلها هنا إذا كان الـ Service يعيد Query Builder
        $apartments = $this->apartmentService->getAllApartments($request->all());

        return ApartmentResource::collection($apartments);
    }

    public function show($id)
    {
        $apartment = $this->apartmentService->getApartmentById($id);

        // تحميل العلاقات الضرورية لعرض التفاصيل
        $apartment->load(['owner', 'images', 'amenities', 'reviews']);

        return new ApartmentResource($apartment);
    }

    public function store(StoreApartmentRequest $request): JsonResponse
    {
        $apartment = $this->apartmentService->createApartment(
            $request->validated(),
            $request->file('images')
        );

        // تحميل العلاقات للرد بالبيانات كاملة
        $apartment->load(['images', 'owner', 'amenities']);

        return response()->json([
            'message' => 'Apartment created successfully',
            'data' => new ApartmentResource($apartment), // هنا استخدمنا data لتوحيد الرد
        ], 201);
    }

    public function update(UpdateApartmentRequest $request, Apartment $apartment): JsonResponse
    {
        Gate::authorize('update', $apartment);

        $updatedApartment = $this->apartmentService->updateApartment(
            $apartment,
            $request->validated(),
            $request->file('images') // <--- تمرير الصور الجديدة هنا
        );

        return response()->json([
            'message' => 'Apartment updated successfully',
            'data' => new ApartmentResource($updatedApartment),
        ]);
    }


    // ... باقي الدوال (destroy, myApartments) كما هي
    public function destroy(Apartment $apartment): JsonResponse
    {
        Gate::authorize('delete', $apartment);
        $this->apartmentService->deleteApartment($apartment);
        return response()->json(['message' => 'Apartment deleted successfully']);
    }

    public function myApartments(Request $request)
    {
        $apartments = $this->apartmentService->getOwnerApartments($request->user()->id);
        return ApartmentResource::collection($apartments);
    }
}
