<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FacebookProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'facebook_profile_link' => [
                'required',
                'url',
                'regex:/facebook\.com/',
            ],
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
            'facebook_profile_link.required' => 'URL Facebook không được để trống.',
            'facebook_profile_link.url' => 'URL phải là đường dẫn hợp lệ.',
            'facebook_profile_link.regex' => 'URL phải là liên kết Facebook hợp lệ.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

