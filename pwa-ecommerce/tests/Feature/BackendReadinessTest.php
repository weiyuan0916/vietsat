<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BackendReadinessTest extends TestCase
{
    public function test_api_routes_are_listable(): void
    {
        $this->artisan('route:list --path=api')
            ->assertExitCode(0);
    }

    public function test_database_can_migrate_fresh_and_seed(): void
    {
        $databasePath = database_path('test-readiness.sqlite');

        if (!file_exists($databasePath)) {
            touch($databasePath);
        }

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', $databasePath);

        DB::purge('sqlite');

        try {
            $this->artisan('migrate:fresh --seed --database=sqlite')
                ->assertExitCode(0);
        } finally {
            DB::purge('sqlite');

            if (file_exists($databasePath)) {
                unlink($databasePath);
            }
        }
    }
}
