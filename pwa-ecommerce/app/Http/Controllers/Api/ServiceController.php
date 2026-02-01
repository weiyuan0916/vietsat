<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Get the default service plan.
     */
    public function default(): JsonResponse
    {
        $service = Service::where('is_active', true)
            ->orderBy('id')
            ->firstOrFail();

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'duration_days' => $service->duration_days,
            'price' => $service->price,
        ]);
    }
}

