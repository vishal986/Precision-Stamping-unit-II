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
        Schema::create('coils', function (Blueprint $table) {
            $table->id();
            $table->string('coil_name');
            $table->string("coil_num")->unique();
            $table->string("job_size");
            $table->string("grade");
            $table->integer('quantity');             // numeric input only
            $table->decimal('weight_value', 10, 3)->nullable();
            $table->enum('weight_unit', ['g', 'kg', 'mt'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coil');
    }
};
