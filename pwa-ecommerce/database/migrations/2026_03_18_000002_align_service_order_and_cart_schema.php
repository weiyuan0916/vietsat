<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->mergeDuplicateCarts('session_id');
        $this->mergeDuplicateCarts('user_id');

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->change();
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->nullOnDelete();
            $table->index('user_id', 'service_orders_user_id_idx');
            $table->index('service_id', 'service_orders_service_id_idx');
            $table->index('status', 'service_orders_status_idx');
            $table->index('expires_at', 'service_orders_expires_at_idx');
            $table->index(['status', 'expires_at'], 'service_orders_status_expires_at_idx');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->unique('user_id', 'carts_user_id_unique');
            $table->unique('session_id', 'carts_session_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('carts_user_id_unique');
            $table->dropUnique('carts_session_id_unique');
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex('service_orders_user_id_idx');
            $table->dropIndex('service_orders_service_id_idx');
            $table->dropIndex('service_orders_status_idx');
            $table->dropIndex('service_orders_expires_at_idx');
            $table->dropIndex('service_orders_status_expires_at_idx');
            $table->dropForeign(['service_id']);
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->restrictOnDelete();
        });
    }

    private function mergeDuplicateCarts(string $column): void
    {
        $duplicates = DB::table('carts')
            ->select($column)
            ->whereNotNull($column)
            ->groupBy($column)
            ->havingRaw('COUNT(*) > 1')
            ->pluck($column);

        foreach ($duplicates as $value) {
            $cartIds = DB::table('carts')
                ->where($column, $value)
                ->orderBy('id')
                ->pluck('id');

            $keeperId = $cartIds->shift();

            foreach ($cartIds as $duplicateId) {
                $items = DB::table('cart_items')
                    ->where('cart_id', $duplicateId)
                    ->get();

                foreach ($items as $item) {
                    $existingItem = DB::table('cart_items')
                        ->where('cart_id', $keeperId)
                        ->where('service_id', $item->service_id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($existingItem) {
                        DB::table('cart_items')
                            ->where('id', $existingItem->id)
                            ->update([
                                'quantity' => $existingItem->quantity + $item->quantity,
                                'subtotal' => $existingItem->subtotal + $item->subtotal,
                                'updated_at' => now(),
                            ]);

                        DB::table('cart_items')->where('id', $item->id)->delete();
                    } else {
                        DB::table('cart_items')
                            ->where('id', $item->id)
                            ->update([
                                'cart_id' => $keeperId,
                                'updated_at' => now(),
                            ]);
                    }
                }

                DB::table('carts')->where('id', $duplicateId)->delete();
            }

            $this->syncCartTotals((int) $keeperId);
        }
    }

    private function syncCartTotals(int $cartId): void
    {
        $subtotal = (float) DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->sum('subtotal');

        $cart = DB::table('carts')->where('id', $cartId)->first();

        if (!$cart) {
            return;
        }

        $tax = (float) ($cart->tax ?? 0);
        $shipping = (float) ($cart->shipping ?? 0);
        $discount = (float) ($cart->discount ?? 0);

        DB::table('carts')
            ->where('id', $cartId)
            ->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $tax + $shipping - $discount,
                'updated_at' => now(),
            ]);
    }
};
