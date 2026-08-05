<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyMemberRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'in:Spouse,Child,Parent,Sibling'],
            'age' => ['required', 'integer', 'min:0', 'max:120'],
            'gender' => ['required', 'in:Male,Female'],
            'blood_group' => ['required', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
        ];
    }
}
