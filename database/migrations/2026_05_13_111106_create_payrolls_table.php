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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('month', 2);
            $table->string('year', 4);
            $table->decimal('total_days', 5, 1);
            $table->decimal('present_days', 5, 1);
            $table->decimal('lwp_days', 5, 1)->default(0);
            
            // Earnings
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('hra', 10, 2);
            $table->decimal('special_allowance', 10, 2);
            $table->decimal('gross_salary', 10, 2);
            
            // Deductions
            $table->decimal('pf_deduction', 10, 2)->default(0);
            $table->decimal('esi_deduction', 10, 2)->default(0);
            $table->decimal('advance_deduction', 10, 2)->default(0);
            $table->decimal('lwp_deduction', 10, 2)->default(0);
            $table->decimal('total_deduction', 10, 2)->default(0);
            
            // Final
            $table->decimal('net_payable', 10, 2);
            $table->enum('status', ['Draft', 'Generated', 'Paid'])->default('Draft');
            $table->date('payment_date')->nullable();
            
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
