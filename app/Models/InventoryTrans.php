<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTrans extends Model
{
    protected $table = 'inven_trans';

    protected $fillable = [
        'warehouse_id_to',
        'warehouse_id_from',
        'trans_number',
        'trans_date',
        'status',
        'note',
        'reason',
        'created_by',
        'posted_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function warehouseFrom()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id_from');
    }

    public function warehouseTo()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id_to');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(InvenTransDetail::class, 'inven_trans_id');
    }
}
