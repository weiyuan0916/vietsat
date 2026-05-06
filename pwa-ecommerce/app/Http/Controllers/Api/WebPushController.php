<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class WebPushController extends Controller
{
    public function vapidPublicKey(): JsonResponse
    {
        $key = config('webpush.vapid.public_key');

        if (empty($key)) {
            return response()->json([
                'status' => false,
                'message' => 'VAPID chưa được cấu hình.',
                'data' => null,
            ], 503);
        }

        return response()->json([
            'status' => true,
            'message' => 'OK',
            'data' => [
                'public_key' => $key,
            ],
        ]);
    }
}
