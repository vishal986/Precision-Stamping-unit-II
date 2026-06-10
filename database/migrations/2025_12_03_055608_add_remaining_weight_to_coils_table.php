<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('coils', function (Blueprint $table) {
            $table->decimal('remaining_weight', 10, 3)->default(0);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coils', function (Blueprint $table) {
            //
        });
    }
};
