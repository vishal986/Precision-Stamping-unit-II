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
        Schema::create('biometric_logs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->index();
            $table->timestamp('punch_time');
            $table->string('device_id')->nullable();
            $table->string('direction')->nullable(); // In/Out if device supports it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_logs');
    }
};
