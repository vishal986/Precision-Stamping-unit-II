<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift', 'name');
    }
}
