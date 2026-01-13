<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // نرجعه true لأن التحقق من صلاحية المستخدم (هل هو المستأجر؟)
        // سيتم التعامل معه في الكونترولر باستخدام Gate::authorize('update', $booking)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'check_in_date' => 'sometimes|date|after:today',
            // نتحقق أن تاريخ المغادرة بعد تاريخ الوصول الموجود في الطلب
            'check_out_date' => 'sometimes|date|after:check_in_date',
        ];
    }
}
