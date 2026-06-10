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
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_item_id')->constrained()->onDelete('cascade');
            $table->date('log_date');
            $table->decimal('quantity_produced', 10, 2)->default(0);
            $table->decimal('quantity_rejected', 10, 2)->default(0);
            $table->string('rejection_reason')->nullable();
            $table->string('operator_name')->nullable();
            $table->string('machine_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};
