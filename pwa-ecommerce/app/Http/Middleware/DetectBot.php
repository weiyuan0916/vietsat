<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bot Detection Middleware
 * 
 * Detects and blocks common bots/crawlers from accessing protected endpoints.
 * Uses multiple detection methods:
 * 1. User-Agent string analysis
 * 2. Known bot patterns
 * 3. Request behavior analysis (can be extended)
 */
class DetectBot
{
    /**
     * List of known bot User-Agent strings (partial matches)
     *
     * @var array
     */
    protected array $botPatterns = [
        // Search Engine Bots
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python',
        // Google Bots
        'googlebot', 'google-inspectiontool', 'adsbot-google', 'mediapartners-google',
        // Bing Bots
        'bingbot', 'msnbot', 'bingpreview',
        // Other Search Bots
        'yandex', 'baidu', 'sogou', 'exabot', 'facebot', 'ia_archiver',
        // Security/Testing Bots
        'nuclei', 'havij', 'sqlmap', 'nikto', 'zap', 'burp',
        // Social Media Bots
        'twitterbot', 'facebookexternalhit', 'linkedinbot', 'pinterest',
        // Monitoring Bots
        'pingdom', 'uptimerobot', 'statuscake', 'newrelic',
        // Common Hack Tools
        'masscan', 'zgrab', 'gobuster', 'dirb', 'wfuzz',
    ];

    /**
     * List of legitimate browser User-Agents that should always be allowed
     *
     * @var array
     */
    protected array $browserPatterns = [
        'chrome', 'firefox', 'safari', 'edge', 'opera', 'msie', 'trident',
        'android', 'iphone', 'ipad', 'mobile', 'vivaldi', 'brave',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent() ?? '';
        
        // If no User-Agent, could be suspicious but allow for now
        if (empty($userAgent)) {
            // Log warning but allow - some legitimate users hide UA
            // In production, you might want to be stricter
        }

        // Check if it's a known bot
        if ($this->isBot($userAgent)) {
            // Log bot attempt
            \Log::channel('bot')->warning('Bot access attempt blocked', [
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            // Return 403 for bots (or 404 to not reveal protection exists)
            // Using 403 for clarity
            return response()->json([
                'status' => false,
                'message' => 'Access denied',
            ], 403);
        }

        // Check for suspicious patterns in headers
        if ($this->hasSuspiciousHeaders($request)) {
            \Log::channel('bot')->warning('Suspicious headers detected', [
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'headers' => $request->headers->all(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Access denied',
            ], 403);
        }

        // Additional check: verify the request is coming from a real browser
        // by checking for expected browser headers
        if (!$this->hasBrowserHeaders($request)) {
            // This might be a bot with a fake UA or a curl/wget script
            \Log::channel('bot')->warning('Missing browser headers', [
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
            ]);
            
            // Don't block yet, just log - might be legitimate edge case
        }

        return $next($request);
    }

    /**
     * Check if User-Agent indicates a bot
     */
    protected function isBot(string $userAgent): bool
    {
        $userAgentLower = strtolower($userAgent);

        // Check against known bot patterns
        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                // Be more strict: if it contains "bot" or similar, it's likely a bot
                // EXCEPT if it also contains browser patterns (some bots fake UAs)
                if ($this->containsBrowser($userAgentLower)) {
                    // Check if it's clearly a bot or just contains the word
                    // e.g., "turbot" contains "bot" but is not a bot
                    // "chrome" + "bot" is suspicious
                    if ($this->isExplicitBotIndicator($userAgentLower)) {
                        return true;
                    }
                    continue;
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user agent contains browser patterns
     */
    protected function containsBrowser(string $userAgent): bool
    {
        foreach ($this->browserPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check for explicit bot indicators in UA string
     */
    protected function isExplicitBotIndicator(string $userAgent): bool
    {
        $explicitBotPatterns = [
            'googlebot', 'bingbot', 'msnbot', 'yandexbot', 'baiduspider',
            'duckduckbot', 'facebot', 'ia_archiver', 'twitterbot',
            'slackbot', 'telegrambot', 'whatsapp', 'botometer',
            'crawler', 'spider', 'scraper', 'curl', 'wget', 'python-requests',
        ];

        foreach ($explicitBotPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for suspicious header combinations
     */
    protected function hasSuspiciousHeaders(Request $request): bool
    {
        // Check for common automated tool headers
        $suspiciousHeaders = [
            'X-Amz-Cf-Id',
            'X-Scan-Title',
            'X-HackerRank',
            'X-Malware-Check',
        ];

        foreach ($suspiciousHeaders as $header) {
            if ($request->hasHeader($header)) {
                return true;
            }
        }

        // Check for missing expected headers on non-GET requests
        if (!$request->isMethod('GET')) {
            // Missing Accept header could indicate automation
            if (!$request->hasHeader('Accept')) {
                // Not necessarily suspicious for all tools, but worth noting
            }

            // Check for common API client headers
            $apiClients = ['postman', 'insomnia', 'swagger'];
            $userAgent = strtolower($request->userAgent() ?? '');

            foreach ($apiClients as $client) {
                if (str_contains($userAgent, $client)) {
                    // Allow API clients but log
                    \Log::channel('bot')->info('API client access', [
                        'client' => $client,
                        'ip' => $request->ip(),
                    ]);
                }
            }
        }

        return false;
    }

    /**
     * Check if request has expected browser headers
     */
    protected function hasBrowserHeaders(Request $request): bool
    {
        // Modern browsers send these headers
        $expectedHeaders = [
            'Accept',
            'Accept-Language',
        ];

        foreach ($expectedHeaders as $header) {
            if (!$request->hasHeader($header)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the client IP address considering proxies
     */
    protected function getClientIp(Request $request): string
    {
        return $request->getClientIp() ?? 'unknown';
    }
}

