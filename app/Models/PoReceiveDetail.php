<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoReceiveDetail extends Model
{
    protected $table = 'po_receive_detail';

    protected $fillable = [
        'po_receive_id',
        'po_detail_id',
        'item_id',
        'qty_received',
        'qty_returned',
        'note',
    ];

    public function receive()
    {
        return $this->belongsTo(PoReceive::class, 'po_receive_id');
    }

    public function poDetail()
    {
        return $this->belongsTo(PoDetail::class, 'po_detail_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
