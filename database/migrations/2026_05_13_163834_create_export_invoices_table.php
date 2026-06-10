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
        Schema::create('export_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date');
            $table->unsignedBigInteger('customer_id');
            $table->string('currency')->default('EUR'); // EUR, USD, INR
            $table->decimal('exchange_rate', 15, 4)->default(1.0000);
            $table->string('incoterms')->nullable(); // FOB, CIF, EXW
            $table->string('vessel_flight_no')->nullable();
            $table->string('container_no')->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('final_destination')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('bank_details')->nullable(); // Bank name, SWIFT, IBAN
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('Draft'); // Draft, Sent, Paid, Cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_invoices');
    }
};
