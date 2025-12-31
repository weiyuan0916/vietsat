<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store PcInfo Request
 *
 * Validates PC information storage request data.
 */
class StorePcInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public API endpoint for PC info collection
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'host_name' => 'nullable|string|max:255',
            'user_name' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'local_ip_address' => 'nullable|ip',
            'public_ip_address' => 'nullable|ip',
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
            'host_name.max' => 'Host name must not exceed 255 characters.',
            'user_name.max' => 'User name must not exceed 255 characters.',
            'password.max' => 'Password must not exceed 255 characters.',
            'local_ip_address.ip' => 'Local IP address must be a valid IP address.',
            'public_ip_address.ip' => 'Public IP address must be a valid IP address.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-detect public IP if not provided
        if (!$this->has('public_ip_address') || !$this->public_ip_address) {
            $this->merge([
                'public_ip_address' => $this->getClientIp(),
            ]);
        }

        // Auto-detect local IP if not provided
        if (!$this->has('local_ip_address') || !$this->local_ip_address) {
            $this->merge([
                'local_ip_address' => $this->getLocalIp(),
            ]);
        }
    }

    /**
     * Get the client's public IP address.
     */
    private function getClientIp(): ?string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if ($this->server($header)) {
                $ip = trim(explode(',', $this->server($header))[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $this->ip();
    }

    /**
     * Get the client's local IP address.
     */
    private function getLocalIp(): ?string
    {
        // For local/private IPs, prefer certain headers
        $localHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($localHeaders as $header) {
            if ($this->server($header)) {
                $ip = trim(explode(',', $this->server($header))[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
                    return $ip;
                }
            }
        }

        return $this->ip();
    }
}
