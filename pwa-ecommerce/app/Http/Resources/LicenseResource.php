<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * License API Resource
 * 
 * Transform license data for API responses.
 */
class LicenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'license_key' => $this->license_key,
            'type' => $this->type,
            'status' => $this->status,
            'max_activations' => $this->max_activations,
            'current_activations' => $this->current_activations,
            'issued_at' => $this->issued_at->toDateTimeString(),
            'expires_at' => $this->expires_at->toDateTimeString(),
            'last_renewed_at' => $this->last_renewed_at?->toDateTimeString(),
            'is_valid' => $this->isValid(),
            'is_expired' => $this->isExpired(),
            'days_remaining' => $this->daysRemaining(),
            'can_activate' => $this->canActivate(),
            'min_app_version' => $this->min_app_version,
            'latest_app_version' => $this->latest_app_version,
            'force_update' => $this->force_update ?? false,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'activations' => LicenseActivationResource::collection($this->whenLoaded('activations')),
        ];

        // Include update file information if available
        if ($this->update_file_path) {
            $data['update_file'] = [
                'download_url' => $this->getUpdateFileUrl(),
                'version' => $this->update_file_version,
                'size' => $this->update_file_size,
                'size_formatted' => $this->getFormattedFileSize(),
                'available' => $this->hasUpdateFile(),
            ];
        }

        return $data;
    }
}

