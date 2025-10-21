<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Renew License Request
 * 
 * Validates license renewal request data.
 */
class RenewLicenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public API endpoint
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'license_key' => 'required|string|max:255',
            'days' => 'nullable|integer|min:1|max:3650', // Max 10 years
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'license_key.required' => 'License key is required.',
            'days.integer' => 'Days must be a valid integer.',
            'days.min' => 'Days must be at least 1.',
            'days.max' => 'Days cannot exceed 3650 (10 years).',
        ];
    }

    /**
     * Get validated days or default to 365.
     */
    public function getDays(): int
    {
        return $this->validated('days', 365);
    }
}

