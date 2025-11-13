<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandDaily extends Model
{
    protected $table = 'demand_daily';

    protected $fillable = [
        'cabang_resto_id',
        'warehouse_id',
        'items_id',
        'date',
        'sales_qty',
        'demand_qty',
        'computed_from',
        'created_at'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'items_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function cabangResto()
    {
        return $this->belongsTo(CabangResto::class, 'cabang_resto_id');
    }
}
