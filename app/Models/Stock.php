<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'item_id',
        'qty',
    ];

    public $timestamps = true;

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
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

        public function scopeCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

     public function scopeWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    // scope by item
    public function scopeItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }
}
