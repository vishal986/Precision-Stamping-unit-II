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
        Schema::create('gatepasses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->enum('type', ['Personal', 'Official'])->default('Personal');
            $table->time('out_time');
            $table->time('in_time')->nullable();
            $table->string('reason')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepasses');
    }
};
