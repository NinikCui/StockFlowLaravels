<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvenTransDetail extends Model
{
    protected $table = 'inven_trans_detail';

    protected $fillable = [
        'items_id',
        'inven_trans_id',
        'qty',
        'note',
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }

    public function header()
    {
        return $this->belongsTo(InventoryTrans::class, 'inven_trans_id');
    }
}
