<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacebookProfileRequest;
use App\Services\FacebookUidExtractorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacebookProfileController extends Controller
{
    private FacebookUidExtractorService $uidExtractor;

    public function __construct(FacebookUidExtractorService $uidExtractor)
    {
        $this->uidExtractor = $uidExtractor;
    }
    /**
     * Validate and parse Facebook profile URL.
     *
     * POST /api/v1/facebook-profiles/validate
     *
     * Request Body:
     * {
     *   "facebook_profile_link": "https://facebook.com/username hoặc https://facebook.com/profile.php?id=123456789"
     * }
     *
     * Response (200) - Valid profile:
     * {
     *   "status": true,
     *   "message": "URL hợp lệ.",
     *   "data": {
     *     "original_url": "https://facebook.com/username",
     *     "normalized_url": "https://www.facebook.com/username",
     *     "profile_id": "username",
     *     "profile_type": "username",
     *     "is_mobile": false,
     *     "is_shortened": false,
     *     "profile_info": {
     *       "username": "username",
     *       "facebook_url": "https://www.facebook.com/username",
     *       "profile_url": "https://www.facebook.com/username"
     *     }
     *   }
     * }
     *
     * Response (200) - Valid profile with numeric ID:
     * {
     *   "status": true,
     *   "message": "URL hợp lệ.",
     *   "data": {
     *     "original_url": "https://facebook.com/profile.php?id=123456789",
     *     "normalized_url": "https://www.facebook.com/profile.php?id=123456789",
     *     "profile_id": "123456789",
     *     "profile_type": "numeric_id",
     *     "is_mobile": false,
     *     "is_shortened": false,
     *     "profile_info": {
     *       "numeric_id": "123456789",
     *       "facebook_url": "https://www.facebook.com/profile.php?id=123456789",
     *       "profile_url": "https://www.facebook.com/profile.php?id=123456789"
     *     }
     *   }
     * }
     *
     * Response (200) - Mobile URL:
     * {
     *   "status": true,
     *   "message": "URL hợp lệ.",
     *   "data": {
     *     "original_url": "https://m.facebook.com/username",
     *     "normalized_url": "https://www.facebook.com/username",
     *     "profile_id": "username",
     *     "profile_type": "username",
     *     "is_mobile": true,
     *     "is_shortened": false,
     *     "profile_info": {
     *       "username": "username",
     *       "facebook_url": "https://www.facebook.com/username",
     *       "profile_url": "https://m.facebook.com/username"
     *     }
     *   }
     * }
     *
     * Response (422) - Invalid URL:
     * {
     *   "status": false,
     *   "message": "URL Facebook không hợp lệ.",
     *   "data": null,
     *   "errors": {
     *     "facebook_profile_link": ["URL phải là liên kết Facebook hợp lệ."]
     *   }
     * }
     */
    public function validate(FacebookProfileRequest $request): JsonResponse
    {
        $url = $request->input('facebook_profile_link');

        $parsed = $this->parseFacebookProfileUrl($url);

        return response()->json([
            'status' => true,
            'message' => 'URL hợp lệ.',
            'data' => $parsed,
        ]);
    }

    /**
     * Validate multiple Facebook profile URLs at once.
     *
     * POST /api/v1/facebook-profiles/validate-batch
     *
     * Request Body:
     * {
     *   "urls": [
     *     "https://facebook.com/username1",
     *     "https://facebook.com/username2"
     *   ]
     * }
     *
     * Response (200):
     * {
     *   "status": true,
     *   "message": "Kiểm tra hàng loạt hoàn tất.",
     *   "data": {
     *     "total": 2,
     *     "valid": 2,
     *     "invalid": 0,
     *     "results": [
     *       {
     *         "url": "https://facebook.com/username1",
     *         "valid": true,
     *         "profile_id": "username1",
     *         "profile_type": "username"
     *       },
     *       {
     *         "url": "https://facebook.com/username2",
     *         "valid": true,
     *         "profile_id": "username2",
     *         "profile_type": "username"
     *       }
     *     ]
     *   }
     * }
     */
    public function validateBatch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'urls' => ['required', 'array', 'min:1'],
            'urls.*' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $urls = $request->input('urls', []);

        $results = [];
        $validCount = 0;
        $invalidCount = 0;

        foreach ($urls as $url) {
            $validator = Validator::make(['url' => $url], [
                'url' => ['required', 'url', 'regex:/facebook\.com/'],
            ]);

            if ($validator->fails()) {
                $results[] = [
                    'url' => $url,
                    'valid' => false,
                    'profile_id' => null,
                    'profile_type' => null,
                    'error' => $validator->errors()->first('url'),
                ];
                $invalidCount++;
            } else {
                $parsed = $this->parseFacebookProfileUrl($url);
                $results[] = [
                    'url' => $url,
                    'valid' => true,
                    'profile_id' => $parsed['profile_id'],
                    'profile_type' => $parsed['profile_type'],
                ];
                $validCount++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Kiểm tra hàng loạt hoàn tất.',
            'data' => [
                'total' => count($urls),
                'valid' => $validCount,
                'invalid' => $invalidCount,
                'results' => $results,
            ],
        ]);
    }

    /**
     * Extract real Facebook UID from a profile URL.
     *
     * This method fetches the actual Facebook profile page and extracts
     * the numeric UID using various patterns found in the HTML.
     *
     * POST /api/v1/facebook-profiles/extract-uid
     *
     * Request Body:
     * {
     *   "facebook_profile_link": "https://facebook.com/username hoặc https://facebook.com/profile.php?id=123456789"
     * }
     *
     * Response (200) - UID found:
     * {
     *   "success": true,
     *   "message": "UID được trích xuất thành công.",
     *   "data": {
     *     "original_url": "https://facebook.com/username",
     *     "normalized_url": "https://www.facebook.com/username",
     *     "profile_id_from_url": "username",
     *     "uid": "100014343376569",
     *     "profile_info": {
     *       "username": "username",
     *       "facebook_url": "https://www.facebook.com/username",
     *       "profile_url": "https://www.facebook.com/username"
     *     }
     *   }
     * }
     *
     * Response (200) - UID not found (profile might be private):
     * {
     *   "success": false,
     *   "message": "Không tìm thấy UID trong trang. Profile có thể riêng tư hoặc đã bị chặn.",
     *   "data": null
     * }
     *
     * Response (422) - Invalid URL:
     * {
     *   "success": false,
     *   "message": "URL Facebook không hợp lệ.",
     *   "data": null,
     *   "errors": {
     *     "facebook_profile_link": ["URL phải là liên kết Facebook hợp lệ."]
     *   }
     * }
     */
    public function extractUid(FacebookProfileRequest $request): JsonResponse
    {
        $url = $request->input('facebook_profile_link');

        // First, parse the URL to get basic info
        $parsed = $this->parseFacebookProfileUrl($url);

        // Try to extract UID from URL first (for URLs with numeric ID)
        $uidFromUrl = $this->uidExtractor->extractUidFromUrl($url);

        // If we got UID from URL, return it directly
        if ($uidFromUrl !== null && $this->uidExtractor->isValidUid($uidFromUrl)) {
            return response()->json([
                'status' => true,
                'success' => true,
                'message' => 'UID được trích xuất thành công từ URL.',
                'data' => array_merge($parsed, [
                    'uid' => $uidFromUrl,
                ]),
            ]);
        }

        // Otherwise, fetch the page to extract real UID
        $result = $this->uidExtractor->extract($url);

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => $result['error'],
                'data' => null,
                'errors' => null,
            ], 422);
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'UID được trích xuất thành công.',
            'data' => array_merge($parsed, [
                'uid' => $result['uid'],
            ]),
        ]);
    }

    /**
     * Parse Facebook profile URL and extract information.
     *
     * @param string $url
     * @return array
     */
    private function parseFacebookProfileUrl(string $url): array
    {
        $originalUrl = $url;
        $isMobile = false;
        $isShortened = false;

        // Normalize URL
        $url = trim($url);
        $url = strtolower($url);

        // Check if it's a mobile URL
        if (str_contains($url, 'm.facebook.com')) {
            $isMobile = true;
            $url = str_replace('m.facebook.com', 'www.facebook.com', $url);
        }

        // Check if it's a shortened URL (fb.me)
        if (str_contains($url, 'fb.me')) {
            $isShortened = true;
        }

        // Remove tracking parameters
        $url = preg_replace('/\?.*$/', '', $url);

        // Remove trailing slash
        $url = rtrim($url, '/');

        // Parse URL to get path
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        // Remove leading slash
        $path = ltrim($path, '/');

        // Determine profile type and ID
        $profileId = null;
        $profileType = 'unknown';

        if (preg_match('/^profile\.php\?id=(\d+)$/', $path, $matches)) {
            // Numeric ID format: facebook.com/profile.php?id=123456789
            $profileId = $matches[1];
            $profileType = 'numeric_id';
        } elseif (preg_match('/^(\w+)$/', $path, $matches)) {
            // Username format: facebook.com/username
            $profileId = $matches[1];
            $profileType = 'username';
        } elseif (preg_match('/^(\d+)$/', $path, $matches)) {
            // Direct numeric ID: facebook.com/123456789
            $profileId = $matches[1];
            $profileType = 'numeric_id';
        }

        // Build normalized URL
        $normalizedUrl = 'https://www.facebook.com/' . $profileId;

        // Build profile info based on type
        $profileInfo = [
            'facebook_url' => $normalizedUrl,
        ];

        if ($profileType === 'username') {
            $profileInfo['username'] = $profileId;
            $profileInfo['profile_url'] = $isMobile
                ? 'https://m.facebook.com/' . $profileId
                : $normalizedUrl;
        } else {
            $profileInfo['numeric_id'] = $profileId;
            $profileInfo['profile_url'] = $isMobile
                ? 'https://m.facebook.com/profile.php?id=' . $profileId
                : 'https://www.facebook.com/profile.php?id=' . $profileId;
        }

        return [
            'original_url' => $originalUrl,
            'normalized_url' => $normalizedUrl,
            'profile_id' => $profileId,
            'profile_type' => $profileType,
            'is_mobile' => $isMobile,
            'is_shortened' => $isShortened,
            'profile_info' => $profileInfo,
        ];
    }

    /**
     * Check if a Facebook profile URL is valid (quick check).
     *
     * GET /api/v1/facebook-profiles/check?url=...
     *
     * Query Parameters:
     * - url: Facebook profile URL to check
     *
     * Response (200):
     * {
     *   "status": true,
     *   "message": "URL hợp lệ.",
     *   "data": {
     *     "valid": true,
     *     "profile_id": "username",
     *     "profile_type": "username"
     *   }
     * }
     */
    public function check(): JsonResponse
    {
        $url = request('url');

        if (! $url) {
            return response()->json([
                'status' => false,
                'message' => 'Thiếu tham số url.',
                'data' => null,
            ], 422);
        }

        $validator = Validator::make(['url' => $url], [
            'url' => ['required', 'url', 'regex:/facebook\.com/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => true,
                'message' => 'URL không hợp lệ.',
                'data' => [
                    'valid' => false,
                    'profile_id' => null,
                    'profile_type' => null,
                ],
            ]);
        }

        $parsed = $this->parseFacebookProfileUrl($url);

        return response()->json([
            'status' => true,
            'message' => 'URL hợp lệ.',
            'data' => [
                'valid' => true,
                'profile_id' => $parsed['profile_id'],
                'profile_type' => $parsed['profile_type'],
            ],
        ]);
    }
}

