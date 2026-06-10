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
        Schema::create('export_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('export_invoice_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->string('hs_code')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_invoice_items');
    }
};
