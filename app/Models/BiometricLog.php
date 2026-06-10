<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricLog extends Model
{
    use HasFactory;

    protected $fillable = ['employee_code', 'punch_time', 'device_id', 'direction'];

    protected $casts = [
        'punch_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_code', 'employee_code');
    }
}
