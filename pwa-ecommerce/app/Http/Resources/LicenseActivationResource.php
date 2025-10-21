<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * License Activation API Resource
 * 
 * Transform license activation data for API responses.
 */
class LicenseActivationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'license_id' => $this->license_id,
            'machine_id' => $this->machine_id,
            'machine_name' => $this->machine_name,
            'ip_address' => $this->ip_address,
            'hardware_info' => $this->hardware_info,
            'status' => $this->status,
            'is_active' => $this->isActive(),
            'activated_at' => $this->activated_at->toDateTimeString(),
            'last_validated_at' => $this->last_validated_at?->toDateTimeString(),
            'deactivated_at' => $this->deactivated_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}

