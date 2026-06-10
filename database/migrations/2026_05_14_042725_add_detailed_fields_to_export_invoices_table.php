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
        Schema::table('export_invoices', function (Blueprint $table) {
            $table->string('exporter_ref')->nullable();
            $table->string('buyer_order_no')->nullable();
            $table->string('eori_no')->nullable();
            $table->string('pre_carriage_by')->nullable();
            $table->string('place_of_receipt')->nullable();
            $table->string('country_of_origin')->default('India');
            $table->string('country_of_final_destination')->nullable();
            $table->text('marks_and_nos')->nullable();
            $table->string('no_and_kind_of_pkgs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('export_invoices', function (Blueprint $table) {
            //
        });
    }
};
