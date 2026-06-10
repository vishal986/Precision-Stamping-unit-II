<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(ProductionOrderItem::class);
    }

    public function coilIssues()
    {
        return $this->hasMany(CoilIssue::class);
    }

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    public function qualityLogs()
    {
        return $this->hasMany(QualityLog::class);
    }
}
