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
        Schema::create('order', function (Blueprint $table) {
            $table->BigIncrements('order_id');
            $table->string('custumer_name');
            $table->string("order_number");
            $table->date("order_date");
            $table->string("item_name");
            $table->string("article_number");
            $table->string("quantity");
            $table->date("delivery_week");
            $table->timestamps();
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
