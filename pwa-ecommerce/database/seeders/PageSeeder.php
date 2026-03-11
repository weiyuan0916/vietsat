<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

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
                'content' => view('pages.privacy')->render(),
                'meta_title' => 'Chính sách bảo mật - Yiki',
                'meta_description' => 'Chính sách bảo mật và quyền riêng tư của Yiki. Tìm hiểu cách chúng tôi thu thập, sử dụng và bảo vệ thông tin của bạn.',
                'is_active' => true,
            ],
            [
                'slug' => 'terms',
                'title' => 'Điều khoản dịch vụ',
                'content' => view('pages.terms')->render(),
                'meta_title' => 'Điều khoản dịch vụ - Yiki',
                'meta_description' => 'Điều khoản và điều kiện sử dụng dịch vụ của Yiki.',
                'is_active' => true,
            ],
        ];

        foreach ($pages as $pageData) {
            // Use firstOrUpdate to be idempotent
            Page::firstOrUpdate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}

