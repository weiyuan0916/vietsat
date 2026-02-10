<?php

namespace Database\Seeders;

use App\Models\PcInfo;
use Illuminate\Database\Seeder;

/**
 * PcInfo Seeder
 *
 * Seeds the pc_infos table with sample data for testing
 */
class PcInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample PC info records
        PcInfo::factory()->create([
            'host_name' => 'DESKTOP-ADMIN01',
            'user_name' => 'admin',
            'local_ip_address' => '192.168.1.100',
            'public_ip_address' => '203.0.113.1',
        ]);

        PcInfo::factory()->create([
            'host_name' => 'LAPTOP-USER01',
            'user_name' => 'john_doe',
            'local_ip_address' => '192.168.1.101',
            'public_ip_address' => '203.0.113.2',
        ]);

        PcInfo::factory()->create([
            'host_name' => 'WORKSTATION-DEV01',
            'user_name' => 'developer',
            'local_ip_address' => '192.168.1.102',
            'public_ip_address' => '203.0.113.3',
        ]);

        // Create additional random records
        PcInfo::factory(10)->create();

        // Create some minimal records
        PcInfo::factory(5)->minimal()->create();

        // Create some recent records
        PcInfo::factory(3)->recent()->create();
    }
}


















