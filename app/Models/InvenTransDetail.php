<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvenTransDetail extends Model
{
    protected $table = 'inven_trans_detail';

    protected $fillable = [
        'stocks_id',
        'inven_trans_id',
        'qty',
        'note'
    ];

    public $timestamps = false;

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stocks_id');
    }

    public function inventoryTrans()
    {
        return $this->belongsTo(InventoryTrans::class, 'inven_trans_id');
    }
}
