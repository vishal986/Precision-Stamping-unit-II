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
        Schema::table('production_order_items', function (Blueprint $table) {
            $table->string('current_stage')->default('Pending')->after('quantity_rejected');
        });

        Schema::table('production_logs', function (Blueprint $table) {
            $table->string('process_type')->nullable()->after('production_order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_order_items', function (Blueprint $table) {
            $table->dropColumn('current_stage');
        });

        Schema::table('production_logs', function (Blueprint $table) {
            $table->dropColumn('process_type');
        });
    }
};
