<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoReceive extends Model
{
    protected $table = 'po_receive';

    protected $fillable = [
        'purchase_order_id',
        'warehouse_id',
        'received_by',
        'received_at',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function details()
    {
        return $this->hasMany(PoReceiveDetail::class, 'po_receive_id');
    }
}
