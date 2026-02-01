<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 50)->unique();
            $table->foreignId('service_id')->constrained()->onDelete('restrict');
            $table->unsignedBigInteger('amount');
            $table->string('status', 20)->default('pending'); // pending, paid, expired
            $table->timestamp('expires_at');
            $table->timestamp('paid_at')->nullable();
            $table->string('bank_txn_id', 100)->nullable()->unique();
            $table->string('facebook_profile_link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};

