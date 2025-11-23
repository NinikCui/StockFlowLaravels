<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    protected $table = 'po_detail';

    protected $fillable = [
        'purchase_order_id',
        'items_id',
        'qty_ordered',
        'unit_price',
        'quality',
        'conversion_to_stock',
        'discount_pct',
        'note_line',
    ];

    public $timestamps = false;

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class, 'po_detail_id');
    }

    public function receives()
    {
        return $this->hasMany(PoReceiveDetail::class, 'po_detail_id');
    }
}
