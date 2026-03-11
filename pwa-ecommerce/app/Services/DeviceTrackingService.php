<?php

namespace App\Services;

use App\Models\DeviceTracking;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceTrackingService
{
    /**
     * Generate device fingerprint from request.
     */
    public function generateFingerprint(Request $request): string
    {
        $data = [
            $request->ip(),
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];

        return hash('sha256', implode('|', $data));
    }

    /**
     * Track device and return/create tracking record.
     */
    public function trackDevice(Request $request): DeviceTracking
    {
        $fingerprint = $this->generateFingerprint($request);

        $deviceData = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $this->getDeviceType($request->userAgent()),
            'browser' => $this->getBrowser($request->userAgent()),
            'os' => $this->getOS($request->userAgent()),
        ];

        return DeviceTracking::firstOrCreate(
            ['device_fingerprint' => $fingerprint],
            $deviceData
        );
    }

    /**
     * Verify if the device matches the order's device.
     */
    public function verifyDevice(Request $request, ServiceOrder $order): bool
    {
        // If order has no device fingerprint, allow (for backward compatibility)
        if (!$order->device_fingerprint) {
            return true;
        }

        $requestFingerprint = $this->generateFingerprint($request);

        // Direct fingerprint match
        if ($requestFingerprint === $order->device_fingerprint) {
            return true;
        }

        // IP match (additional security check)
        if ($order->ip_address && $request->ip() === $order->ip_address) {
            return true;
        }

        return false;
    }

    /**
     * Extract device type from user agent.
     */
    private function getDeviceType(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            return 'Mobile';
        }

        if (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    /**
     * Extract browser from user agent.
     */
    private function getBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (preg_match('/chrome/i', $userAgent)) {
            return 'Chrome';
        }

        if (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        }

        if (preg_match('/safari/i', $userAgent)) {
            return 'Safari';
        }

        if (preg_match('/edge/i', $userAgent)) {
            return 'Edge';
        }

        return 'Other';
    }

    /**
     * Extract OS from user agent.
     */
    private function getOS(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        if (preg_match('/windows/i', $userAgent)) {
            return 'Windows';
        }

        if (preg_match('/mac/i', $userAgent)) {
            return 'macOS';
        }

        if (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        }

        if (preg_match('/android/i', $userAgent)) {
            return 'Android';
        }

        if (preg_match('/ios|iphone|ipad/i', $userAgent)) {
            return 'iOS';
        }

        return 'Other';
    }
}

