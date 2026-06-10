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
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date_given');
            $table->decimal('amount_given', 10, 2);
            $table->decimal('emi_amount', 10, 2);
            $table->decimal('remaining_balance', 10, 2);
            $table->enum('status', ['Active', 'Completed'])->default('Active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_advances');
    }
};
