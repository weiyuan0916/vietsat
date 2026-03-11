<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            // Add user_id for linking orders to users
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->after('service_id');

            // Add device fingerprint for security
            $table->string('device_fingerprint', 255)->nullable()->after('facebook_profile_link');
            $table->string('ip_address', 45)->nullable()->after('device_fingerprint');
            $table->string('user_agent', 500)->nullable()->after('ip_address');

            // Add processing timestamps
            $table->timestamp('processing_started_at')->nullable()->after('paid_at');
            $table->timestamp('processing_completed_at')->nullable()->after('processing_started_at');

            // Add extension result
            $table->text('extension_result')->nullable()->after('processing_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'device_fingerprint',
                'ip_address',
                'user_agent',
                'processing_started_at',
                'processing_completed_at',
                'extension_result',
            ]);
        });
    }
};

