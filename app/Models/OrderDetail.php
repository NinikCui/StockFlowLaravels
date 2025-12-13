<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_detail';

    protected $fillable = [
        'pos_order_id',
        'products_id',
        'qty',
        'price',
        'discount_pct',
        'note_line',
    ];

    public $timestamps = false;

    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }
}
