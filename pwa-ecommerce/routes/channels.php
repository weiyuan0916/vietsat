<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| Broadcast Channels (API-Only Setup)
|--------------------------------------------------------------------------
|
| Channel authorization for Laravel Reverb with API-only backend (Framework7 SPA).
| 
| Authentication Flow:
| 1. Frontend calls POST /api/v1/broadcasting/auth with socket_id and channel_name
| 2. BroadcastingAuthController validates order and returns auth signature
| 3. This channel callback provides additional server-side validation
|
| Note: CSRF protection is not needed here as authentication is done via
| the API endpoint which validates the request.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Order Channels
|--------------------------------------------------------------------------
|
| Private channel for order payment status updates.
| Format: private-order.{orderCode} - e.g., private-order.ORD-ABC123XYZ
|
| Authorization is handled via:
| - API endpoint: POST /api/v1/broadcasting/auth (primary)
| - This callback: Additional server-side validation (backup)
|
| Validation:
| 1. Order exists and is not expired
| 2. Optional: IP consistency check
|
*/
Broadcast::channel('order.{orderCode}', function ($user, $orderCode) {
    // Additional server-side validation
    // The primary auth is done via API endpoint
    $order = \App\Models\ServiceOrder::where('order_code', $orderCode)->first();

    if (!$order) {
        \Log::channel('bot')->warning('Invalid order code access', [
            'order_code' => $orderCode,
            'ip' => Request::ip(),
        ]);
        return false;
    }

    if ($order->expires_at->isPast()) {
        \Log::channel('bot')->warning('Expired order access', [
            'order_code' => $orderCode,
            'expires_at' => $order->expires_at,
            'ip' => Request::ip(),
        ]);
        return false;
    }

    // Log for monitoring
    \Log::channel('order')->info('Order channel authorized via callback', [
        'order_code' => $orderCode,
        'status' => $order->status,
        'ip' => Request::ip(),
    ]);

    return true;
});

/*
|--------------------------------------------------------------------------
| Payment Channels (Alias)
|--------------------------------------------------------------------------
|
| Alternative channel naming for frontend compatibility.
| Format: private-payment.{orderCode}
|
*/
Broadcast::channel('payment.{orderCode}', function ($user, $orderCode) {
    $order = \App\Models\ServiceOrder::where('order_code', $orderCode)->first();

    if (!$order || $order->expires_at->isPast()) {
        return false;
    }

    return true;
});
