<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage bookings') ?? false;
    }

    public function rules(): array
    {
        return [
            'new_date' => ['required', 'date', 'after_or_equal:today'],
            'new_time' => ['required', 'date_format:H:i'],
        ];
    }
}
