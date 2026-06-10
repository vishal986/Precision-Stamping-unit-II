<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'customer_id',
        'buyer_details',
        'currency',
        'exchange_rate',
        'incoterms',
        'vessel_flight_no',
        'container_no',
        'port_of_loading',
        'port_of_discharge',
        'final_destination',
        'payment_terms',
        'bank_details',
        'total_amount',
        'status',
        'exporter_ref',
        'buyer_order_no',
        'eori_no',
        'pre_carriage_by',
        'place_of_receipt',
        'country_of_origin',
        'country_of_final_destination',
        'marks_and_nos',
        'no_and_kind_of_pkgs',
    ];

    public function customer()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(ExportInvoiceItem::class);
    }
}
