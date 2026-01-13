<?php

namespace App\Http\Requests\Favorite;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // التحقق من تسجيل الدخول يتم عبر الـ Middleware
    }

    public function rules(): array
    {
        return [
            'apartment_id' => 'required|exists:apartments,id',
        ];
    }
}
