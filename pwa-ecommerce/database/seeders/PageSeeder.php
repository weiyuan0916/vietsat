<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\View;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'privacy',
                'title' => 'Chính sách bảo mật',
                'content' => $this->resolvePageContent(
                    'pages.privacy',
                    '<h1>Chính sách bảo mật</h1><p>Nội dung đang được cập nhật.</p>'
                ),
                'meta_title' => 'Chính sách bảo mật - Yiki',
                'meta_description' => 'Chính sách bảo mật và quyền riêng tư của Yiki. Tìm hiểu cách chúng tôi thu thập, sử dụng và bảo vệ thông tin của bạn.',
                'is_active' => true,
            ],
            [
                'slug' => 'terms',
                'title' => 'Điều khoản dịch vụ',
                'content' => $this->resolvePageContent(
                    'pages.terms',
                    '<h1>Điều khoản dịch vụ</h1><p>Nội dung đang được cập nhật.</p>'
                ),
                'meta_title' => 'Điều khoản dịch vụ - Yiki',
                'meta_description' => 'Điều khoản và điều kiện sử dụng dịch vụ của Yiki.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }

    private function resolvePageContent(string $viewName, string $fallbackContent): string
    {
        if (View::exists($viewName)) {
            return view($viewName)->render();
        }

        return $fallbackContent;
    }
}

