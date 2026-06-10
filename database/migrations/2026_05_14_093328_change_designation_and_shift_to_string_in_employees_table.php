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
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['designation_id']);
            $table->dropForeign(['shift_id']);
            
            // Rename and change to string
            $table->dropColumn(['designation_id', 'shift_id']);
            $table->string('designation')->nullable()->after('department_id');
            $table->string('shift')->nullable()->after('designation');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['designation', 'shift']);
            $table->unsignedBigInteger('designation_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('shift_id')->nullable()->after('designation_id');
            
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('set null');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
        });
    }
};
