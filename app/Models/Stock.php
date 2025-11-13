<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'items_id',
        'warehouse_id',
        'qty',
        'exp_date',
        'received_at',
        'updated_at'
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

    public function invenTransDetails()
    {
        return $this->hasMany(InvenTransDetail::class, 'stocks_id');
    }

    public function productionIssuesDetails()
    {
        return $this->hasMany(ProductionIssuesDetail::class, 'stocks_id');
    }

    public function stocksAdjustmentDetail()
    {
        return $this->hasMany(StocksAdjustmentDetail::class, 'stocks_id');
    }
}
