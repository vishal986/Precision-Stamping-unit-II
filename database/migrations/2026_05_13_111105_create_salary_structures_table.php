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
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->unique();
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->boolean('pf_applicable')->default(true);
            $table->boolean('esi_applicable')->default(true);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
