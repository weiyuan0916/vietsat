<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// #region DEBUG MODE INSTRUMENTATION
class DebugLogger {
    private static $logFile = '/Users/adward/Herd/vietsat/.cursor/debug.log';
    
    public static function log(string $hypothesisId, string $location, string $message, array $data = []): void {
        $entry = json_encode([
            'id' => 'log_' . time() . '_' . substr(md5(uniqid()), 0, 6),
            'timestamp' => microtime(true),
            'sessionId' => 'debug-session',
            'runId' => 'initial',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'message' => $message,
            'data' => $data,
        ], JSON_UNESCAPED_SLASHES);
        
        @file_put_contents(self::$logFile, $entry . "\n", FILE_APPEND | LOCK_EX);
    }
}
// #endregion

/**
 * Controller for Laravel Reverb Channel Authorization
 *
 * Handles API-based authentication for private channels in a SPA/API-only setup.
 *
 * NOTE: We use manual HMAC-SHA256 instead of Pusher SDK's socket_auth()
 * because the SDK's socket_auth() method returns malformed auth strings
 * (missing socket_id in the signature).
 *
 * Pusher Protocol Auth Format: app_key:socket_id:signature
 * Signature = HMAC-SHA256(socket_id:channel_name, secret)
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
     *   "channel_name": "private-payment.ORD-XXXXXXXXXX"
     * }
     *
     * Response (200) - Authorized:
     * {
     *   "auth": "app_key:socket_id:signature"
     * }
     */
    public function authorize(Request $request): JsonResponse
    {
        // Log incoming request
        $socketId = $request->input('socket_id');
        $channelName = $request->input('channel_name');
        $orderCode = $request->input('order_code');

        DebugLogger::log('H1,H2,H3,H4,H5', __FILE__ . ':' . __LINE__, 'Incoming auth request', [
            'socket_id' => $socketId,
            'channel_name' => $channelName,
            'order_code' => $orderCode,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Log::info('[BroadcastingAuth] Incoming request', [
            'socket_id' => $socketId,
            'channel_name' => $channelName,
            'order_code' => $orderCode,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Validate required fields
        if (empty($socketId) || empty($channelName)) {
            Log::warning('[BroadcastingAuth] Missing required fields', [
                'socket_id' => $socketId,
                'channel_name' => $channelName,
            ]);

            return response()->json(['error' => 'Missing socket_id or channel_name'], 403);
        }

        // Handle Pusher public channels (order.{code}) - NO auth required
        // These channels are used for order status updates via Pusher
        if (Str::startsWith($channelName, 'order.')) {
            Log::info('[BroadcastingAuth] Public channel request - no auth needed', [
                'channel' => $channelName,
                'socket_id' => $socketId,
            ]);

            // For public channels, return empty response (Pusher will allow subscription)
            return response()->json([]);
        }

        // Handle Pusher presence channels (presence-order.{code}) - minimal auth
        if (Str::startsWith($channelName, 'presence-order.')) {
            $orderCode = Str::after($channelName, 'presence-order.');

            Log::info('[BroadcastingAuth] Presence channel request', [
                'channel' => $channelName,
                'order_code' => $orderCode,
            ]);

            // Validate order exists
            $order = \App\Models\ServiceOrder::where('order_code', $orderCode)->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 403);
            }

            // Return presence auth (user info without private channel signature)
            $userData = [
                'user_id' => $orderCode,
                'user_info' => [
                    'order_code' => $orderCode,
                ],
            ];

            return response()->json([
                'auth' => '',
                'presence' => [
                    'channel' => $channelName,
                    'data' => json_encode($userData),
                ],
            ]);
        }

        // Extract order code from channel name if not provided
        if (empty($orderCode)) {
            if (Str::startsWith($channelName, 'private-order.')) {
                $orderCode = Str::after($channelName, 'private-order.');
            } elseif (Str::startsWith($channelName, 'private-payment.')) {
                $orderCode = Str::after($channelName, 'private-payment.');
            }
        }

        // Validate order
        $validation = $this->validateOrder($orderCode, $socketId, $request);

        if (!$validation['valid']) {
            Log::warning('[BroadcastingAuth] Order validation failed', [
                'order_code' => $orderCode,
                'reason' => $validation['reason'],
                'channel' => $channelName,
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => $validation['reason']], 403);
        }

        // Generate auth signature (manual HMAC-SHA256)
        $auth = $this->generateAuth($socketId, $channelName);

        Log::info('[BroadcastingAuth] Auth generated', [
            'order_code' => $orderCode,
            'channel' => $channelName,
            'socket_id' => $socketId,
            'auth_preview' => substr($auth, 0, 50) . '...',
        ]);

        DebugLogger::log('H1,H2,H3,H4,H5', __FILE__ . ':' . __LINE__, 'Auth generated, returning response', [
            'auth' => $auth,
            'auth_parts_count' => count(explode(':', $auth)),
        ]);

        // Return ONLY the auth field
        DebugLogger::log('H1,H2,H3,H4,H5', __FILE__ . ':' . __LINE__, 'Auth returned to frontend', [
            'auth' => $auth,
            'auth_parts' => explode(':', $auth),
            'auth_parts_count' => count(explode(':', $auth)),
        ]);
        return response()->json(['auth' => $auth]);
    }

    /**
     * Generate auth signature using HMAC-SHA256
     *
     * Pusher protocol format: app_key:socket_id:signature
     * Where signature = HMAC-SHA256(socket_id:channel_name, secret)
     *
     * IMPORTANT: For private channels, the channel_name in signature
     * should be WITHOUT the 'private-' prefix (e.g., "payment.ORD-XXX"
     * instead of "private-payment.ORD-XXX").
     */
    protected function generateAuth(string $socketId, string $channelName): string
    {
        // #region HYPOTHESIS 1: Credential Mismatch
        // Get credentials from environment AND config
        $key = env('REVERB_APP_KEY');
        $secret = env('REVERB_APP_SECRET');
        $appId = env('REVERB_APP_ID');
        
        // Also get from config for comparison
        $configKey = config('reverb.apps.apps.0.key');
        $configSecret = config('reverb.apps.apps.0.secret');
        $configAppId = config('reverb.apps.apps.0.app_id');

        DebugLogger::log('H1', __FILE__ . ':' . __LINE__, 'H1: Comparing env vs config credentials', [
            'env_key' => substr($key, 0, 8) . '...',
            'env_key_len' => strlen($key),
            'config_key' => substr($configKey, 0, 8) . '...',
            'config_key_len' => strlen($configKey),
            'env_secret_len' => strlen($secret),
            'config_secret_len' => strlen($configSecret),
            'env_secret_prefix' => substr($secret, 0, 8) . '...',
            'config_secret_prefix' => substr($configSecret, 0, 8) . '...',
            'env_app_id' => $appId,
            'config_app_id' => $configAppId,
            'keys_match' => $key === $configKey,
            'secrets_match' => $secret === $configSecret,
        ]);

        if (empty($key) || empty($secret) || empty($appId)) {
            throw new \RuntimeException('Reverb credentials not configured');
        }

        // #endregion

        // #region HYPOTHESIS 2 & 3: String-to-Sign Format & Channel Name
        // Test BOTH channel formats: with and without 'private-' prefix
        $channelWithPrefix = $channelName;
        $channelWithoutPrefix = Str::startsWith($channelName, 'private-') 
            ? Str::after($channelName, 'private-') 
            : $channelName;

        DebugLogger::log('H2,H3', __FILE__ . ':' . __LINE__, 'H2,H3: Testing both channel formats', [
            'original_channel' => $channelName,
            'with_prefix' => $channelWithPrefix,
            'without_prefix' => $channelWithoutPrefix,
        ]);

        // String to sign: socket_id:channel_name
        $stringToSignWithPrefix = $socketId . ':' . $channelWithPrefix;
        $stringToSignWithoutPrefix = $socketId . ':' . $channelWithoutPrefix;

        DebugLogger::log('H2,H3', __FILE__ . ':' . __LINE__, 'H2,H3: Generated strings to sign', [
            'with_prefix' => $stringToSignWithPrefix,
            'without_prefix' => $stringToSignWithoutPrefix,
        ]);

        // Generate HMAC-SHA256 signatures for BOTH formats
        $signatureWithPrefix = hash_hmac('sha256', $stringToSignWithPrefix, $secret);
        $signatureWithoutPrefix = hash_hmac('sha256', $stringToSignWithoutPrefix, $secret);

        DebugLogger::log('H2,H3', __FILE__ . ':' . __LINE__, 'H2,H3: Generated signatures', [
            'signature_with_prefix' => $signatureWithPrefix,
            'signature_without_prefix' => $signatureWithoutPrefix,
        ]);

        // Format: app_key:socket_id:signature (BOTH formats)
        $authWithPrefix = $key . ':' . $socketId . ':' . $signatureWithPrefix;
        $authWithoutPrefix = $key . ':' . $socketId . ':' . $signatureWithoutPrefix;

        DebugLogger::log('H2,H3', __FILE__ . ':' . __LINE__, 'H2,H3: Generated auth strings', [
            'auth_with_prefix' => $authWithPrefix,
            'auth_without_prefix' => $authWithoutPrefix,
            'with_prefix_parts' => explode(':', $authWithPrefix),
            'without_prefix_parts' => explode(':', $authWithoutPrefix),
        ]);

        // #endregion

        // #region HYPOTHESIS 4: Alternative Format (key:socket_id:app_id:signature)
        // Some implementations use app_id in the auth string
        $stringToSignWithAppId = $socketId . ':' . $channelWithoutPrefix . ':' . $appId;
        $signatureWithAppId = hash_hmac('sha256', $stringToSignWithAppId, $secret);
        $authWithAppId = $key . ':' . $socketId . ':' . $signatureWithAppId;

        DebugLogger::log('H4', __FILE__ . ':' . __LINE__, 'H4: Alternative format with app_id', [
            'string_to_sign' => $stringToSignWithAppId,
            'auth_with_app_id' => $authWithAppId,
        ]);

        // #endregion

        // #region HYPOTHESIS 5: Secret Encoding Issue (try raw vs base64)
        // Check if secret needs different encoding
        $secretHex = bin2hex($secret);
        $signatureHexWithPrefix = hash_hmac('sha256', $stringToSignWithoutPrefix, $secretHex);

        DebugLogger::log('H5', __FILE__ . ':' . __LINE__, 'H5: Testing secret encoding', [
            'secret_raw_length' => strlen($secret),
            'secret_hex_length' => strlen($secretHex),
            'signature_with_hex_secret' => $signatureHexWithPrefix,
        ]);

        // #endregion

        // For now, use WITHOUT prefix (most common format)
        $channelForSignature = $channelWithoutPrefix;
        $stringToSign = $stringToSignWithoutPrefix;
        $signature = $signatureWithoutPrefix;
        $auth = $authWithoutPrefix;

        // Log ALL auth variants for comparison
        DebugLogger::log('H1,H2,H3,H4,H5', __FILE__ . ':' . __LINE__, 'Final auth - ALL VARIANTS', [
            'socket_id' => $socketId,
            'channel' => $channelName,
            'env_secret_len' => strlen($secret),
            'config_secret_len' => strlen($configSecret),
            'V1_without_prefix' => $authWithoutPrefix,  // socket_id:channel (no private-)
            'V2_with_prefix' => $authWithPrefix,        // socket_id:private-channel
            'V3_with_app_id' => $authWithAppId,         // socket_id:channel:app_id
            'V1_sig' => $signatureWithoutPrefix,
            'V2_sig' => $signatureWithPrefix,
            'V3_sig' => $signatureWithAppId,
        ]);

        // Log the final auth details
        Log::debug('[BroadcastingAuth] Auth generated', [
            'socket_id' => $socketId,
            'channel' => $channelName,
            'channel_for_signature' => $channelForSignature,
            'string_to_sign' => $stringToSign,
            'signature' => $signature,
            'auth' => $auth,
        ]);

        DebugLogger::log('H1,H2,H3,H4,H5', __FILE__ . ':' . __LINE__, 'Final auth selected', [
            'selected_auth' => $auth,
            'selected_channel' => $channelForSignature,
            'selected_string_to_sign' => $stringToSign,
            'all_hypotheses_logged' => true,
        ]);

        return $auth;
    }

    /**
     * Validate order for channel access
     */
    protected function validateOrder(?string $orderCode, string $socketId, Request $request): array
    {
        if (empty($orderCode)) {
            return ['valid' => false, 'reason' => 'Invalid channel: order code not found'];
        }

        $order = \App\Models\ServiceOrder::where('order_code', $orderCode)->first();

        if (!$order) {
            return ['valid' => false, 'reason' => 'Order not found: ' . $orderCode];
        }

        if ($order->expires_at->isPast()) {
            return ['valid' => false, 'reason' => 'Order has expired'];
        }

        // Check user agent for bots
        $userAgent = $request->userAgent() ?? '';

        if ($this->isBot($userAgent)) {
            return ['valid' => false, 'reason' => 'Automated requests not allowed'];
        }

        Log::info('[BroadcastingAuth] Order validated', [
            'order_code' => $orderCode,
            'status' => $order->status,
            'expires_at' => $order->expires_at,
        ]);

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

        // Explicit bot patterns
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

        // Check for generic bot patterns
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
}
