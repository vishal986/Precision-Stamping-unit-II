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
        Schema::table('coil_issues', function (Blueprint $table) {
            $table->foreignId('production_order_id')->nullable()->constrained('production_orders')->nullOnDelete()->after('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coil_issues', function (Blueprint $table) {
            $table->dropForeign(['production_order_id']);
            $table->dropColumn('production_order_id');
        });
    }
};
