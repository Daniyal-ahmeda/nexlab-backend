<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'diagnostic_test_id' => ['required', 'string', 'exists:diagnostic_tests,id'],
            'partner_lab_id' => ['required', 'string', 'exists:partner_labs,id'],
            'is_home_collection' => ['boolean'],
            'date' => ['required', 'date'],
            'time_slot' => ['required', 'string'],
            'patient_name' => ['required', 'string', 'max:255'],
        ];
    }
}
