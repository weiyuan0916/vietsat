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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->decimal('balance', 12, 2)->default(0.00)->after('phone');
            $table->enum('role', ['customer', 'vendor', 'admin'])->default('customer')->after('balance');
            $table->boolean('is_online')->default(false)->after('role');
            $table->timestamp('last_seen_at')->nullable()->after('is_online');
            $table->text('address')->nullable()->after('last_seen_at');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->default('Vietnam')->after('state');
            $table->string('postal_code')->nullable()->after('country');
            
            // Indexes
            $table->index('role');
            $table->index('is_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone',
                'balance',
                'role',
                'is_online',
                'last_seen_at',
                'address',
                'city',
                'state',
                'country',
                'postal_code'
            ]);
        });
    }
};
