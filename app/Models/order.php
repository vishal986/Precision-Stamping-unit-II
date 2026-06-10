<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $table="order";
    protected $primaryKey = 'order_id';
    public $incrementing = true;              // Auto-increment primary key
    protected $keyType = 'int';               // Key type is integer

    protected $fillable=[   
        'custumer_name',
        'order_number',
        'order_date',
        'item_name',
        'article_number',
        'quantity',
        'delivery_week',
    ];
}
