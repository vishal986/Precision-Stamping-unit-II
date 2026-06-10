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
        Schema::create('coil_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coil_id');
            $table->unsignedBigInteger('department_id');
            $table->decimal('issued_weight', 10, 3);
            $table->date('issue_date');
            $table->string('issued_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('coil_id')->references('id')->on('coils')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coil_issues');
    }
};
