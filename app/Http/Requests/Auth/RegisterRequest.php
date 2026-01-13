<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:20|unique:users',
            'user_type' => 'required|in:tenant,owner',
            // 'otp' => 'required|string', // تم الحذف
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'profile_image' => 'required|image|max:2048',
            'id_image' => 'required|image|max:2048',
        ];
    }
}
