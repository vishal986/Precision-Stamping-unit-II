<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coil extends Model
{
    use SoftDeletes;
    use HasFactory;
    // Specify table name (optional if following convention)
    protected $table = 'coils';

    // Set primary key (default is 'id', change if your PK is different)
    protected $primaryKey = 'id'; // Keep 'id' as PK;

    // Allow mass assignment
    protected $fillable = [
        'coil_name',
        'coil_num',
        'job_size',
        'grade',
        'quantity',
        'weight_value',
        'weight_unit',
        'remaining_weight',
    ];
    protected static function booted()
    {
        // UPDATE
        static::updating(function ($coil) {
            // block changing original weight if issues exist unless explicitly allowed
            if ($coil->isDirty('weight_value') && !$coil->allow_reset) {
                if ($coil->issues()->exists()) {
                    throw new \Exception(
                        'This coil already has issued material. Reset not allowed.'
                    );
                }
            }
        });
    }

    // 👇 virtual property (not DB column)
    protected $allow_reset = false;

    public function allowReset()
    {
        $this->allow_reset = true;
        return $this;
    }

    // If you want timestamps (created_at, updated_at)
    public $timestamps = true;
    public function issues()
    {
        return $this->hasMany(CoilIssue::class);
    }
}
