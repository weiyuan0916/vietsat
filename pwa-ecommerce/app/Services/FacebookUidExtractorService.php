<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service to extract Facebook UID from profile URLs.
 *
 * This service fetches Facebook profile pages and extracts the numeric UID
 * using various patterns found in the HTML content.
 *
 * Based on extension approach that looks for:
 * - <meta property="al:android:url" content="fb://profile/100014343376569" />
 * - fb://profile/<digits>
 * - "profile_id":"<digits>"
 * - "entity_id":"<digits>"
 */
class FacebookUidExtractorService
{
    /**
     * User agent string to mimic browser requests.
     */
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Timeout for HTTP requests in seconds.
     */
    private const TIMEOUT = 15;

    /**
     * Extract Facebook UID from a profile URL.
     *
     * @param string $profileUrl The Facebook profile URL
     * @return array{success: bool, uid?: string, error?: string}
     */
    public function extract(string $profileUrl): array
    {
        try {
            // Normalize the URL
            $url = $this->normalizeUrl($profileUrl);

            // Try to fetch the profile page
            $html = $this->fetchProfilePage($url);

            if ($html === null) {
                return [
                    'success' => false,
                    'error' => 'Không thể truy cập trang Facebook. Profile có thể riêng tư hoặc không tồn tại.',
                ];
            }

            // Extract UID from HTML
            $uid = $this->extractUidFromHtml($html);

            if ($uid === null) {
                return [
                    'success' => false,
                    'error' => 'Không tìm thấy UID trong trang. Profile có thể riêng tư hoặc đã bị chặn.',
                ];
            }

            return [
                'success' => true,
                'uid' => $uid,
            ];
        } catch (\Exception $e) {
            Log::error('Facebook UID extraction error: ' . $e->getMessage(), [
                'url' => $profileUrl,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Lỗi khi trích xuất UID: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract UID from HTML content using multiple patterns.
     *
     * @param string $html The HTML content
     * @return string|null The extracted UID or null if not found
     */
    public function extractUidFromHtml(string $html): ?string
    {
        if (empty($html)) {
            return null;
        }

        // Pattern 1: <meta property="al:android:url" content="fb://profile/100014343376569" />
        $pattern = '/<meta[^>]*property=["\']al:android:url["\'][^>]*content=["\']fb:\/\/profile\/(\d+)["\'][^>]*>/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 2: Any occurrence of fb://profile/<digits>
        $pattern = '/fb:\/\/profile\/(\d+)/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 3: "profile_id":"<digits>" or "profile_id":"<digits>"
        $pattern = '/["\']profile_id["\']\s*[:=]\s*["\']?(\d+)["\']?/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 4: "entity_id":"<digits>" or "entity_id":"<digits>"
        $pattern = '/["\']entity_id["\']\s*[:=]\s*["\']?(\d+)["\']?/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 5: "userID":"<digits>" or userID:<digits>
        $pattern = '/["\']userID["\']\s*[:=]\s*["\']?(\d+)["\']?/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 6: "page_id":"<digits>" (for Page profiles)
        $pattern = '/["\']page_id["\']\s*[:=]\s*["\']?(\d+)["\']?/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 7: data-profileid="<digits>" attribute
        $pattern = '/data-profileid=["\'](\d+)["\']/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        // Pattern 8: "content":{"fb://profile/<digits>"}
        $pattern = '/fb:\/\/profile\/(\d+)/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Normalize Facebook profile URL.
     *
     * @param string $url The original URL
     * @return string Normalized URL
     */
    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        $url = strtolower($url);

        // Handle mobile URLs
        if (str_contains($url, 'm.facebook.com')) {
            $url = str_replace('m.facebook.com', 'www.facebook.com', $url);
        }

        // Handle mbasic URLs
        if (str_contains($url, 'mbasic.facebook.com')) {
            $url = str_replace('mbasic.facebook.com', 'www.facebook.com', $url);
        }

        // Remove query parameters and fragments
        $url = preg_replace('/[\?#].*$/', '', $url);

        // Ensure https
        if (!str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    /**
     * Fetch Facebook profile page HTML.
     *
     * @param string $url The normalized profile URL
     * @return string|null The HTML content or null on failure
     */
    private function fetchProfilePage(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'none',
                'Cache-Control' => 'max-age=0',
            ])
                ->withOptions([
                    'verify' => false, // Skip SSL verification for Facebook
                    'timeout' => self::TIMEOUT,
                    'connect_timeout' => 5,
                ])
                ->get($url);

            if ($response->successful()) {
                return $response->body();
            }

            Log::warning('Facebook fetch failed with status: ' . $response->status(), [
                'url' => $url,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Facebook fetch error: ' . $e->getMessage(), [
                'url' => $url,
            ]);

            return null;
        }
    }

    /**
     * Check if a string looks like a valid Facebook UID.
     *
     * @param string $uid The string to check
     * @return bool
     */
    public function isValidUid(string $uid): bool
    {
        return preg_match('/^\d+$/', $uid) === 1 && strlen($uid) >= 6;
    }

    /**
     * Extract UID from URL path directly (without fetching HTML).
     * This is a fallback for URLs that already contain the numeric ID.
     *
     * @param string $url The Facebook profile URL
     * @return string|null The UID from URL or null
     */
    public function extractUidFromUrl(string $url): ?string
    {
        $url = $this->normalizeUrl($url);

        // Pattern: facebook.com/profile.php?id=123456789
        if (preg_match('/facebook\.com\/profile\.php\?id=(\d+)/i', $url, $matches)) {
            return $matches[1];
        }

        // Pattern: facebook.com/123456789 (numeric ID directly)
        if (preg_match('/facebook\.com\/(\d{6,})(?:\/|$)/i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}


