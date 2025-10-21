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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key', 255)->unique();
            $table->enum('type', ['trial', 'standard', 'premium', 'enterprise'])->default('standard');
            $table->enum('status', ['active', 'expired', 'suspended', 'revoked'])->default('active');
            $table->integer('max_activations')->default(1); // How many machines can use this key
            $table->integer('current_activations')->default(0);
            $table->timestamp('issued_at');
            $table->timestamp('expires_at');
            $table->timestamp('last_renewed_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional license info
            $table->timestamps();
            $table->softDeletes();

            $table->index('license_key');
            $table->index('status');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};

