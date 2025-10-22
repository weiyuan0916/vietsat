<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->string('min_app_version')->nullable()->after('metadata')->comment('Minimum app version required');
            $table->string('latest_app_version')->nullable()->after('min_app_version')->comment('Latest app version available');
            $table->boolean('force_update')->default(false)->after('latest_app_version')->comment('Force user to update if below min version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['min_app_version', 'latest_app_version', 'force_update']);
        });
    }
};
