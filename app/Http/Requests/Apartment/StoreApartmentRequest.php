<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // التحقق من أن المستخدم هو مالك
        return Auth::user()->user_type === 'owner';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'governorate' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'amenities' => 'required|array',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:2048',
        ];
    }
}
