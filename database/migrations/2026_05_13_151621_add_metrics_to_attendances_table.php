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
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('late_minutes')->default(0)->after('punch_out');
            $table->integer('early_exit_minutes')->default(0)->after('late_minutes');
            $table->boolean('is_auto_calculated')->default(false)->after('early_exit_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['late_minutes', 'early_exit_minutes', 'is_auto_calculated']);
        });
    }
};
