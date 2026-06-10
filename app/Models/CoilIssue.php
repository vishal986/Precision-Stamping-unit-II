<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;


class CoilIssue extends Model
{
    protected $fillable = [
        'coil_id',
        'department_id',
        'production_order_id',
        'issued_weight',
        'issue_unit',
        'issue_date',
        'issued_by',
        'remarks',
    ];

    public function coil()
    {
        return $this->belongsTo(Coil::class)->withTrashed();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }
}

