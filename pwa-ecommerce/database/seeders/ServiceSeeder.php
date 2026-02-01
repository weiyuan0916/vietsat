<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: Update if exists, create if not
        Service::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Default Plan',
                'duration_days' => 90, // 3 months
                'price' => 100000, // 100,000 VND
                'is_active' => true,
            ]
        );
    }
}

