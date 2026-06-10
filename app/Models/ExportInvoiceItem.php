<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'export_invoice_id',
        'item_id',
        'quantity',
        'unit_price',
        'hs_code',
        'order_number',
        'order_date',
        'total_price',
    ];

    public function invoice()
    {
        return $this->belongsTo(ExportInvoice::class, 'export_invoice_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
