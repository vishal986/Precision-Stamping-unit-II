<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function logs()
    {
        return $this->hasMany(ProductionLog::class);
    }
}
