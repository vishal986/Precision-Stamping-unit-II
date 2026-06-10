<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'log_date' => 'date',
        'quantity_produced' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
    ];

    public function productionOrderItem()
    {
        return $this->belongsTo(ProductionOrderItem::class);
    }
}
