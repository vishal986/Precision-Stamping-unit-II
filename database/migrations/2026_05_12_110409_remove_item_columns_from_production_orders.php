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
        // First migrate data
        $orders = \DB::table('production_orders')->get();
        foreach($orders as $order) {
            if ($order->item_id) {
                \DB::table('production_order_items')->insert([
                    'production_order_id' => $order->id,
                    'item_id' => $order->item_id,
                    'quantity_planned' => $order->quantity_planned,
                    'quantity_produced' => $order->quantity_produced,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn(['item_id', 'quantity_planned', 'quantity_produced']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->constrained('items')->restrictOnDelete();
            $table->decimal('quantity_planned', 10, 2)->nullable();
            $table->decimal('quantity_produced', 10, 2)->default(0);
        });
    }
};
