<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
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
            'type' => ['required', 'in:Edfaaly,Mobi Cash,Sadad,Tyssir,Tadawul,Sahel,Moamalat,Cash'],
            'number' => ['required', 'string', 'max:255'],
            'expiry' => ['nullable', 'string', 'max:255'],
            'is_default' => ['boolean'],
            'firebase_token' => ['required', 'string'],
        ];
    }
}
