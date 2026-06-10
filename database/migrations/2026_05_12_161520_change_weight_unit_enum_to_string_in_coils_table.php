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
        Schema::table('coils', function (Blueprint $table) {
            $table->string('weight_unit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coils', function (Blueprint $table) {
            // Note: Cannot easily revert string back to enum without data loss risk,
            // but we can enforce it via model validation anyway.
        });
    }
};
