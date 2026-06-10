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
            $table->decimal('hourly_rate', 10, 2)->default(0)->after('special_allowance');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('worked_hours', 10, 2)->default(0)->after('present_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_structures', function (Blueprint $table) {
            $table->dropColumn('hourly_rate');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('worked_hours');
        });
    }
};
