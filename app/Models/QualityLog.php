<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'production_order_item_id',
        'ok_qty',
        'rejected_qty',
        'remarks',
        'inspector_id',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function productionOrderItem()
    {
        return $this->belongsTo(ProductionOrderItem::class, 'production_order_item_id');
    }
}
