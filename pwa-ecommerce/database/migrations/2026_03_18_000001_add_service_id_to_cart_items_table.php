<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->dropUnique(['cart_id', 'product_id']);
            $table->unique(['cart_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique(['cart_id', 'service_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->unique(['cart_id', 'product_id']);
        });
    }
};
