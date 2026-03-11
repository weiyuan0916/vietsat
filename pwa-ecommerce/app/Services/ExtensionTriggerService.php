<?php

namespace App\Services;

use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtensionTriggerService
{
    /**
     * Trigger extension to process Facebook account.
     *
     * This service is called when payment is successful.
     * It sends the Facebook URL to the extension for processing.
     */
    public function triggerExtension(ServiceOrder $order): bool
    {
        try {
            // Get Facebook URL from order
            $facebookUrl = $order->facebook_profile_link;

            if (!$facebookUrl) {
                Log::warning('[ExtensionTrigger] No Facebook URL found in order', [
                    'order_code' => $order->order_code,
                ]);
                return false;
            }

            // Option 1: Send to a webhook that triggers extension (if configured)
            $extensionWebhookUrl = config('services.extension.webhook_url');

            if ($extensionWebhookUrl) {
                $response = Http::timeout(30)->post($extensionWebhookUrl, [
                    'order_code' => $order->order_code,
                    'facebook_url' => $facebookUrl,
                    'action' => 'process_facebook_approval',
                    'timestamp' => now()->toIso8601String(),
                ]);

                if ($response->successful()) {
                    Log::info('[ExtensionTrigger] Extension triggered successfully via webhook', [
                        'order_code' => $order->order_code,
                        'facebook_url' => $facebookUrl,
                    ]);

                    // Update order processing status
                    $order->update([
                        'processing_started_at' => now(),
                    ]);

                    return true;
                }

                Log::error('[ExtensionTrigger] Failed to trigger extension via webhook', [
                    'order_code' => $order->order_code,
                    'response' => $response->body(),
                ]);
            }

            // Option 2: Store in queue for external worker to process
            // This would be handled by a separate worker process

            Log::info('[ExtensionTrigger] Order ready for extension processing', [
                'order_code' => $order->order_code,
                'facebook_url' => $facebookUrl,
                'note' => 'Waiting for extension worker to pickup',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[ExtensionTrigger] Error triggering extension', [
                'order_code' => $order->order_code,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark extension processing as completed.
     */
    public function markProcessingCompleted(ServiceOrder $order, array $result = []): void
    {
        $order->update([
            'processing_completed_at' => now(),
            'extension_result' => json_encode($result),
        ]);

        Log::info('[ExtensionTrigger] Extension processing completed', [
            'order_code' => $order->order_code,
            'result' => $result,
        ]);
    }
}

