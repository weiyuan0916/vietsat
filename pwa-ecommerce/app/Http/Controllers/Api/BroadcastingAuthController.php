<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Controller for Laravel Reverb Channel Authorization
 * 
 * Handles API-based authentication for private channels in a SPA/API-only setup.
 * This replaces the need for CSRF token authentication when using Blade views.
 */
class BroadcastingAuthController extends Controller
{
    /**
     * Authorize access to a private channel
     * 
     * POST /api/v1/broadcasting/auth
     * 
     * Request Body:
     * {
     *   "socket_id": "socket123.456",
     *   "channel_name": "private-order.ORD-XXXXXXXXXX",
     *   "order_code": "ORD-XXXXXXXXXX" (optional, extracted from channel_name)
     * }
     * 
     * Response (200) - Authorized:
     * {
     *   "authorized": true,
     *   "auth": "socket123.456:signature_hash"
     * }
     * 
     * Response (403) - Unauthorized:
     * {
     *   "authorized": false,
     *   "error": "Unauthorized message"
     * }
     */
    public function authorize(Request $request): JsonResponse
    {
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');
        $orderCode = $request->input('order_code');

        // Validate required fields
        if (empty($socketId) || empty($channelName)) {
            return $this->unauthorized('Missing required parameters');
        }

        // Only handle order channels
        if (!Str::startsWith($channelName, 'private-order.') && 
            !Str::startsWith($channelName, 'private-payment.')) {
            return $this->unauthorized('Invalid channel type');
        }

        // Extract order code from channel name if not provided
        if (empty($orderCode)) {
            if (Str::startsWith($channelName, 'private-order.')) {
                $orderCode = Str::after($channelName, 'private-order.');
            } elseif (Str::startsWith($channelName, 'private-payment.')) {
                $orderCode = Str::after($channelName, 'private-payment.');
            }
        }

        // Validate order exists and is valid
        $validation = $this->validateOrder($orderCode, $request);
        
        if (!$validation['valid']) {
            Log::channel('bot')->warning('Broadcasting auth failed', [
                'order_code' => $orderCode,
                'reason' => $validation['reason'],
                'socket_id' => $socketId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->unauthorized($validation['reason']);
        }

        // Generate auth signature
        // Laravel Reverb uses app secret to sign the response
        $appSecret = config('reverb.apps.0.secret') ?? config('broadcasting.connections.reverb.secret');
        
        if (empty($appSecret)) {
            Log::error('Reverb app secret not configured');
            return $this->unauthorized('Server configuration error');
        }

        $authString = $socketId . ':' . hash_hmac('sha256', $channelName . ':' . $orderCode, $appSecret);

        // Log successful authorization
        Log::channel('order')->info('Broadcasting auth success', [
            'order_code' => $orderCode,
            'channel' => $channelName,
            'socket_id' => $socketId,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'authorized' => true,
            'auth' => $authString,
            'order_code' => $orderCode,
        ]);
    }

    /**
     * Validate order for channel access
     */
    protected function validateOrder(string $orderCode, Request $request): array
    {
        // Check if order exists
        $order = \App\Models\ServiceOrder::where('order_code', $orderCode)->first();

        if (!$order) {
            return [
                'valid' => false,
                'reason' => 'Order not found',
            ];
        }

        // Check if order is expired
        if ($order->expires_at->isPast()) {
            return [
                'valid' => false,
                'reason' => 'Order has expired',
            ];
        }

        // Check if order is already paid (no need to listen anymore)
        if ($order->status === 'paid') {
            // Still allow access but log it
            Log::channel('order')->info('Order already paid, allowing access', [
                'order_code' => $orderCode,
                'status' => $order->status,
            ]);
        }

        // Additional security: Check IP consistency
        // Only if the order was created from a different IP
        // Note: In production, you might want to be more lenient for mobile users
        // who might switch between WiFi and cellular
        
        // Validate user agent (basic bot detection)
        $userAgent = $request->userAgent() ?? '';
        $isBot = $this->isBot($userAgent);

        if ($isBot) {
            return [
                'valid' => false,
                'reason' => 'Automated requests not allowed',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check if user agent indicates a bot
     */
    protected function isBot(string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }

        $userAgentLower = strtolower($userAgent);

        $explicitBotPatterns = [
            'googlebot', 'bingbot', 'msnbot', 'yandexbot', 'baiduspider',
            'duckduckbot', 'facebot', 'ia_archiver', 'curl', 'wget', 
            'python', 'nuclei', 'havij', 'sqlmap', 'nikto', 'zap', 'burp',
            'pingdom', 'uptimerobot', 'statuscake', 'newrelic',
        ];

        foreach ($explicitBotPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        // Check for generic bot patterns (only if no browser pattern found)
        $browserPatterns = ['chrome', 'firefox', 'safari', 'edge', 'opera', 'android', 'iphone'];
        $hasBrowser = false;

        foreach ($browserPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                $hasBrowser = true;
                break;
            }
        }

        if (!$hasBrowser) {
            $genericBotPatterns = ['bot', 'crawler', 'spider', 'scraper'];
            foreach ($genericBotPatterns as $pattern) {
                if (str_contains($userAgentLower, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return unauthorized response
     */
    protected function unauthorized(string $message): JsonResponse
    {
        return response()->json([
            'authorized' => false,
            'error' => $message,
        ], 403);
    }
}

