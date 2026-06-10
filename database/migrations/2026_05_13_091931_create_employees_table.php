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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // Documents & Bank
            $table->string('aadhaar_number')->nullable();
            $table->string('aadhaar_file_path')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('pan_file_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('ifsc_code')->nullable();

            // Employment Details
            $table->date('joining_date')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->string('grade')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable(); // self-referencing for reporting manager

            // Status
            $table->enum('status', ['Active', 'Inactive', 'Terminated', 'Resigned'])->default('Active');
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
