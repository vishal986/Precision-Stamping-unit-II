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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('medical_allowance', 10, 2)->default(0)->after('special_allowance');
            $table->decimal('conveyance_allowance', 10, 2)->default(0)->after('medical_allowance');
            $table->decimal('employer_pf', 10, 2)->default(0)->after('pf_deduction');
            $table->decimal('employer_esi', 10, 2)->default(0)->after('esi_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['medical_allowance', 'conveyance_allowance', 'employer_pf', 'employer_esi']);
        });
    }
};
