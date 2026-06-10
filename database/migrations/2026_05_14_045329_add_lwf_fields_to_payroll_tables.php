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
        Schema::table('salary_structures', function (Blueprint $table) {
            $table->boolean('lwf_applicable')->default(true)->after('esi_applicable');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('lwf_deduction', 10, 2)->default(0)->after('esi_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_structures', function (Blueprint $table) {
            $table->dropColumn('lwf_applicable');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('lwf_deduction');
        });
    }
};
