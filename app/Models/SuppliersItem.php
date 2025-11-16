<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppliersItem extends Model
{
    protected $table = 'suppliers_item';

    protected $fillable = [
        'suppliers_id',
        'items_id',
        'price',
        'min_order_tqy',
        'last_price_update',
    ];

    public $timestamps = true;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }
}
