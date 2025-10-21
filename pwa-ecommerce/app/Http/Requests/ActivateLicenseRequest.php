<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Activate License Request
 * 
 * Validates license activation request data.
 */
class ActivateLicenseRequest extends FormRequest
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
            'machine_id' => 'required|string|max:255',
            'machine_name' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'hardware_info' => 'nullable|array',
            'hardware_info.cpu' => 'nullable|string',
            'hardware_info.ram' => 'nullable|string',
            'hardware_info.os' => 'nullable|string',
            'hardware_info.disk' => 'nullable|string',
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
            'machine_id.required' => 'Machine ID is required.',
            'ip_address.ip' => 'Invalid IP address format.',
        ];
    }
}

