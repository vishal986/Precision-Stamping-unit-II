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
            $table->decimal('medical_allowance', 10, 2)->default(0)->after('special_allowance');
            $table->decimal('conveyance_allowance', 10, 2)->default(0)->after('medical_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_structures', function (Blueprint $table) {
            $table->dropColumn(['medical_allowance', 'conveyance_allowance']);
        });
    }
};
