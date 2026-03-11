<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Get page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $page = Page::getBySlug($slug);

            if (!$page) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy trang.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy thông tin trang thành công.',
                'data' => [
                    'id' => $page->id,
                    'slug' => $page->slug,
                    'title' => $page->title,
                    'content' => $page->content,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Get privacy policy.
     */
    public function privacy(): JsonResponse
    {
        return $this->show('privacy');
    }

    /**
     * Get terms of service.
     */
    public function terms(): JsonResponse
    {
        return $this->show('terms');
    }
}

