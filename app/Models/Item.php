<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    public function boms()
    {
        return $this->hasMany(Bom::class);
    }
}
