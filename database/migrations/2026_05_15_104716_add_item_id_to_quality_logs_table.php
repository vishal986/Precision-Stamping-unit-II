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
        Schema::table('quality_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('production_order_item_id')->nullable()->after('production_order_id');
            $table->foreign('production_order_item_id')->references('id')->on('production_order_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quality_logs', function (Blueprint $table) {
            $table->dropForeign(['production_order_item_id']);
            $table->dropColumn('production_order_item_id');
        });
    }
};
