<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentPending;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'facebook_profile_link' => ['required', 'url', 'regex:/facebook\.com/'],
        ]);

        $service = Service::where('is_active', true)->firstOrFail();

        return DB::transaction(function () use ($request, $service) {
            $order = ServiceOrder::create([
                'order_code' => 'ORD-' . Str::upper(Str::random(10)),
                'service_id' => $service->id,
                'amount' => $service->price,
                'status' => ServiceOrder::STATUS_PENDING,
                'expires_at' => now()->addMinutes(5),
                'facebook_profile_link' => $request->facebook_profile_link,
            ]);

            event(new PaymentPending($order));

            return response()->json([
                'order_code' => $order->order_code,
                'amount' => $order->amount,
                'expires_at' => $order->expires_at->toIso8601String(),
                'qr_content' => 'bank:' . $order->order_code . ':' . $order->amount,
            ], 201);
        });
    }

    public function show(string $orderCode): JsonResponse
    {
        $order = ServiceOrder::where('order_code', $orderCode)->firstOrFail();

        return response()->json([
            'order_code' => $order->order_code,
            'amount' => $order->amount,
            'status' => $order->status,
            'expires_at' => $order->expires_at->toIso8601String(),
            'paid_at' => $order->paid_at?->toIso8601String(),
        ]);
    }
}
